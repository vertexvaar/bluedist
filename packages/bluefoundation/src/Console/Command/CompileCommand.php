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
use VerteXVaaR\BlueFoundation\PackageExtras;

use function array_key_last;
use function CoStack\Lib\concat_paths;
use function CoStack\Lib\mkdir_deep;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function getcwd;
use function is_dir;
use function json_decode;
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
        $rootPath = getcwd();

        $bootstrapPath = concat_paths($rootPath, 'bootstrap');
        if (!mkdir_deep($bootstrapPath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $bootstrapPath));
        }

        $this->compilePackageExtras($rootPath, $bootstrapPath);
        $this->compileContainer($input, $output, $bootstrapPath);

        $this->dumpBootstrap($rootPath, $bootstrapPath);

        return self::SUCCESS;
    }

    private function compilePackageExtras(string $rootPath, string $bootstrapPath): void
    {
        $rootPathLength = strlen($rootPath);

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
        $code = $this->renderPackageExtras($root['name'], $pathsCode);
        $packageExtrasFilePath = concat_paths($bootstrapPath, 'PackageExtras.php');
        file_put_contents($packageExtrasFilePath, $code);
        require $packageExtrasFilePath;
    }

    private function renderPackageExtras(string $rootPackageName, string $pathsCode): string
    {
        return <<<PHP
            <?php
            
            declare(strict_types=1);
            
            namespace VerteXVaaR\BlueFoundation;
            
            use function CoStack\Lib\concat_paths;
            
            readonly class PackageExtras
            {
                public string \$rootPath;
                public function __construct(
                    public string \$rootPackageName = '$rootPackageName',
                    private array \$paths = $pathsCode,
                ) {
                    \$this->rootPath = realpath(__DIR__ . '/../');
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

    private function compileContainer(InputInterface $input, OutputInterface $output, string $bootstrapPath): void
    {
        $container = new ContainerBuilder();
        $container->set('_input', $input);
        $container->set('_output', $output);
        $container->setAlias(ContainerInterface::class, 'service_container');

        $packageExtrasDefinition = new Definition(PackageExtras::class);
        $packageExtrasDefinition->setPublic(true);
        $packageExtrasDefinition->setShared(true);
        $container->setDefinition(PackageExtras::class, $packageExtrasDefinition);

        $this->loadServices($container, $output);
        $container->compile();

        $dumper = new PhpDumper($container);
        file_put_contents(
            concat_paths($bootstrapPath, 'DI.php'),
            $dumper->dump(['class' => 'DI', 'namespace' => 'VerteXVaaR\\BlueFoundation']),
        );
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

    private function dumpBootstrap(string $rootPath, string $bootstrapPath): void
    {
        $file = concat_paths($bootstrapPath, 'bootstrap.php');
        file_put_contents(
            $file,
            <<<PHP
                <?php
                
                use Symfony\Component\Dotenv\Dotenv;
                use VerteXVaaR\BlueWeb\ErrorHandler\ErrorHandler;

                \$rootDir = dirname(__DIR__) . DIRECTORY_SEPARATOR;
                putenv('VXVR_BS_ROOT=' . \$rootDir);
                
                require(__DIR__ . '/../vendor/autoload.php');
                require(__DIR__ . '/DI.php');
                require(__DIR__ . '/PackageExtras.php');

                \$dotenvFile = \$rootDir . '.env';
                if (file_exists(\$dotenvFile)) {
                    \$dotenv = new Dotenv();
                    \$dotenv->usePutenv();
                    \$dotenv->loadEnv(\$dotenvFile, null, 'dev', [], true);
                }

                \$localDotenvFile = \$rootDir . '.local.env';
                if (file_exists(\$localDotenvFile)) {
                    \$dotenv = new Dotenv();
                    \$dotenv->usePutenv();
                    \$dotenv->loadEnv(\$localDotenvFile, null, 'dev', [], true);
                }
                
                if (empty(ini_get('date.timezone'))) {
                    date_default_timezone_set('UTC');
                }

                \$errorHandler = new ErrorHandler();
                \$errorHandler->register();
                PHP,
        );
    }

}
