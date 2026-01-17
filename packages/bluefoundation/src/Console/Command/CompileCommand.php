<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueFoundation\Console\Command;

use Composer\InstalledVersions;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use VerteXVaaR\BlueFoundation\Composer\Steps\Step;
use VerteXVaaR\BlueFoundation\Generated\PackageExtras;

use function array_key_last;
use function CoStack\Lib\concat_paths;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function getcwd;
use function getenv;
use function is_dir;
use function json_decode;
use function mkdir;
use function putenv;
use function rtrim;
use function sprintf;
use function str_starts_with;
use function strlen;
use function substr;
use function var_export;

use const JSON_THROW_ON_ERROR;

class CompileCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('compile');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!getenv('VXVR_BS_ROOT')) {
            putenv('VXVR_BS_ROOT=' . rtrim(getcwd(), '/') . '/');
        }
        $rootPath = getenv('VXVR_BS_ROOT');
        $rootPathLength = strlen($rootPath);

        $output->writeln(sprintf("VXVR_BS_ROOT=%s", $rootPath), OutputInterface::VERBOSITY_VERBOSE);

        $container = new ContainerBuilder();
        $container->set('_input', $input);
        $container->set('_output', $output);
        $container->setAlias(ContainerInterface::class, 'service_container');

        $installed = InstalledVersions::getAllRawData();
        $installed = $installed[array_key_last($installed)];
        $root = $installed['root'];
        $versions = $installed['versions'];
        $packages = $versions;
        $packages[$root['name']] = $root;

        $packagePaths = [];
        foreach ($packages as $packageName => $package) {
            $packageBlueSprintsExtras = [];
            if (!isset($package['install_path'])) {
                continue;
            }
            $installPath = $package['install_path'];
            $composerFile = concat_paths($installPath, 'composer.json');
            $composerJson = file_get_contents($composerFile);
            $packageComposer = json_decode($composerJson, true, 512, JSON_THROW_ON_ERROR);
            if (empty($packageComposer['extra']['vertexvaar/bluesprints'])) {
                continue;
            }
            foreach ($packageComposer['extra']['vertexvaar/bluesprints'] as $name => $path) {
                $fullPath = concat_paths($installPath, $path);
                if (str_starts_with($fullPath, $rootPath)) {
                    $fullPath = substr($fullPath, $rootPathLength);
                }
                $packageBlueSprintsExtras[$name] = $fullPath;
            }
            $packagePaths[$packageName] = $packageBlueSprintsExtras;
        }

        $pathsCode = var_export($packagePaths, true);

        $generatedClassesFolder = __DIR__ . '/../../Generated';
        if (
            !is_dir($generatedClassesFolder)
            && !mkdir($generatedClassesFolder, 0777, true)
            && !is_dir($generatedClassesFolder)
        ) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $generatedClassesFolder));
        }
        $packageExtras = concat_paths($generatedClassesFolder, 'PackageExtras.php');

        $code = $this->renderPackageExtras($root['name'], $pathsCode);
        file_put_contents($packageExtras, $code);
        require $packageExtras;

        $packageExtrasDefinition = new Definition(PackageExtras::class);
        $packageExtrasDefinition->setPublic(true);
        $packageExtrasDefinition->setShared(true);
        $container->setDefinition(PackageExtras::class, $packageExtrasDefinition);

        $this->loadServices($container, $output);
        $container->compile();

        $dumper = new PhpDumper($container);
        file_put_contents(
            __DIR__ . '/../../Generated/DI.php',
            $dumper->dump(['class' => 'DI', 'namespace' => 'VerteXVaaR\\BlueFoundation\\Generated']),
        );
        return self::SUCCESS;
    }

    private function loadServices(ContainerBuilder $container, OutputInterface $output): void
    {
        $errorOutput = $output instanceof ConsoleOutput ? $output->getErrorOutput() : $output;
        $packageExtras = $container->get(PackageExtras::class);
        foreach ($packageExtras->getPackageNames() as $packageName) {
            $servicesPath = $packageExtras->getPath($packageName, 'services');

            if (null === $servicesPath) {
                $output->writeln(
                    sprintf('Package %s does not define extra.vertexvaar/bluesprints.services, skipping', $packageName),
                    OutputInterface::VERBOSITY_VERY_VERBOSE,
                );
                return;
            }

            if (!is_dir($servicesPath)) {
                $errorOutput->writeln(
                    sprintf(
                        'Package %s defines extra.vertexvaar/bluesprints.services, but the directory "%s" does not exist',
                        $packageName,
                        $servicesPath,
                    ),
                );
                return;
            }

            if (file_exists(concat_paths($servicesPath, 'services.yaml'))) {
                $output->writeln(
                    sprintf('Loading services.yaml from package %s', $packageName),
                    OutputInterface::VERBOSITY_VERBOSE,
                );
                $loader = new YamlFileLoader($container, new FileLocator($servicesPath));
                $loader->load('services.yaml');
            }
            if (file_exists(concat_paths($servicesPath, 'services.php'))) {
                $output->writeln(
                    sprintf('Loading services.php from package %s', $packageName),
                    OutputInterface::VERBOSITY_VERBOSE,
                );
                $loader = new PhpFileLoader($container, new FileLocator($servicesPath));
                $loader->load('services.php');
            }
        }
    }

    private function renderPackageExtras(string $rootPackageName, string $pathsCode): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueFoundation\Generated;

use function array_keys;use function CoStack\Lib\concat_paths;

readonly class PackageExtras
{
    public string \$rootPath;
    public function __construct(
        public string \$rootPackageName = '$rootPackageName',
        private array \$paths = $pathsCode,
    ) {
        \$this->rootPath = getenv('VXVR_BS_ROOT');
    }

    public function getPath(string \$package, string \$type): ?string
    {
        if (!isset(\$this->paths[\$package][\$type])) {
            return null;
        }
        return concat_paths(\$this->rootPath, \$this->paths[\$package][\$type]);
    }
    
    public function getPackageNames(): array
    {
        return array_keys(\$this->paths);
    }
}
PHP;
    }
}
