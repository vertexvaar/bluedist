<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueContainer\Composer\Steps;

use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use VerteXVaaR\BlueContainer\Generated\PackageExtras;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;

use function CoStack\Lib\concat_paths;
use function file_exists;
use function file_put_contents;
use function is_dir;
use function sprintf;

readonly class CompileDependencyInjectionContainer implements Step
{
    public function run(ContainerBuilder $container): void
    {
        $io = $container->get('io');
        /** @var PackageIterator $packageIterator */
        $packageIterator = $container->get('package_iterator');
        $packageIterator->iterate(fn(PackageInterface $package) => $this->loadServices($container, $package));

        $container->compile();

        $dumper = new PhpDumper($container);
        file_put_contents(
            __DIR__ . '/../../Generated/DI.php',
            $dumper->dump(['class' => 'DI', 'namespace' => 'VerteXVaaR\\BlueContainer\\Generated']),
        );

        $io->write('Generated container');
    }

    private function loadServices(ContainerBuilder $container, PackageInterface $package): void
    {
        $io = $container->get('io');

        $packageExtras = $container->get(PackageExtras::class);
        $packageName = $package->getName();
        $servicesPath = $packageExtras->getPath($packageName, 'services');

        if (null === $servicesPath) {
            $io->write(
                sprintf('Package %s does not define extra.vertexvaar/bluesprints.services, skipping', $packageName),
                true,
                IOInterface::VERY_VERBOSE,
            );
            return;
        }

        if (!is_dir($servicesPath)) {
            $io->writeError(
                sprintf(
                    'Package %s defines extra.vertexvaar/bluesprints.services, but the directory "%s" does not exist',
                    $packageName,
                    $servicesPath,
                ),
            );
            return;
        }

        if (file_exists(concat_paths($servicesPath, 'services.yaml'))) {
            $io->write(
                sprintf('Loading services.yaml from package %s', $packageName),
                true,
                IOInterface::VERBOSE,
            );
            $loader = new YamlFileLoader($container, new FileLocator($servicesPath));
            $loader->load('services.yaml');
        }
        if (file_exists(concat_paths($servicesPath, 'services.php'))) {
            $io->write(
                sprintf('Loading services.php from package %s', $packageName),
                true,
                IOInterface::VERBOSE,
            );
            $loader = new PhpFileLoader($container, new FileLocator($servicesPath));
            $loader->load('services.php');
        }
    }
}
