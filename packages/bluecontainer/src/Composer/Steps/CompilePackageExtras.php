<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueContainer\Composer\Steps;

use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use VerteXVaaR\BlueContainer\Generated\PackageExtras;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;

use function CoStack\Lib\concat_paths;
use function file_put_contents;
use function getenv;
use function is_dir;
use function mkdir;
use function sprintf;
use function str_replace;
use function strlen;
use function substr;
use function var_export;

readonly class CompilePackageExtras implements Step
{
    public function run(ContainerBuilder $container): void
    {
        $composer = $container->get('composer');
        $installationManager = $composer->getInstallationManager();

        $rootPath = getenv('VXVR_BS_ROOT');
        $rootPathLength = strlen($rootPath);

        /** @var PackageIterator $packageIterator */
        $packageIterator = $container->get('package_iterator');
        $paths = $packageIterator->map(
            static function (PackageInterface $package) use ($installationManager, $rootPath, $rootPathLength): ?array {
                $installPath = $package instanceof RootPackageInterface
                    ? $rootPath
                    : $installationManager->getInstallPath($package);

                $extra = [];
                foreach ($package->getExtra()['vertexvaar/bluesprints'] ?? [] as $name => $path) {
                    $extra[$name] = substr(concat_paths($installPath, $path), $rootPathLength);
                }
                if (empty($extra)) {
                    return null;
                }
                return [$package->getName() => $extra];
            },
        );

        $rootPath = getenv('VXVR_BS_ROOT');
        $pathsCode = var_export($paths, true);
        $pathsCode = str_replace($rootPath, '', $pathsCode);

        $rootPackageName = $composer->getPackage()->getName();

        $code = $this->renderPackageExtras($rootPackageName, $pathsCode);
        $generatedClassesFolder = concat_paths(__DIR__, '/../../Generated/');
        if (
            !is_dir($generatedClassesFolder)
            && !mkdir($generatedClassesFolder, 0777, true)
            && !is_dir($generatedClassesFolder)
        ) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $generatedClassesFolder));
        }
        $packageExtras = concat_paths($generatedClassesFolder, 'PackageExtras.php');
        file_put_contents($packageExtras, $code);
        require $packageExtras;

        $packageExtrasDefinition = new Definition(PackageExtras::class);
        $packageExtrasDefinition->setPublic(true);
        $packageExtrasDefinition->setShared(true);
        $container->setDefinition(PackageExtras::class, $packageExtrasDefinition);
    }

    public function renderPackageExtras(string $rootPackageName, string $pathsCode): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueContainer\Generated;

use function CoStack\Lib\concat_paths;

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
}
PHP;
    }
}
