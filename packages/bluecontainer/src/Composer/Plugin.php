<?php

namespace VerteXVaaR\BlueContainer\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use VerteXVaaR\BlueContainer\DI;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;

use function CoStack\Lib\concat_paths;
use function file_exists;
use function getcwd;
use function getenv;
use function is_dir;
use function putenv;
use function sprintf;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private Composer $composer;
    private IOInterface $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'post-autoload-dump' => 'postAutoloadDump',
        ];
    }

    public function postAutoloadDump(): void
    {
        $this->io->write('Generating container');

        if (!getenv('VXVR_BS_ROOT')) {
            putenv('VXVR_BS_ROOT=' . getcwd());
        }
        $this->io->write(sprintf("VXVR_BS_ROOT=%s", getenv('VXVR_BS_ROOT')), true, IOInterface::VERBOSE);

        $autoloadFile = $this->composer->getConfig()->get('vendor-dir') . '/autoload.php';
        if (!file_exists($autoloadFile)) {
            $this->io->writeError('Autoload not yet dumped');
            return;
        }
        require $autoloadFile;

        $container = new ContainerBuilder();
        $container->set('composer', $this->composer);
        $container->set('io', $this->io);

        $container->setAlias(ContainerInterface::class, 'service_container');

        $packageIterator = new PackageIterator($this->composer);
        $packageIterator->iterate(
            fn(PackageInterface $package, string $path) => $this->loadServices($container, $package, $path)
        );

        $this->loadServices($container, $this->composer->getPackage(), getenv('VXVR_BS_ROOT'));

        $container->compile();

        $dumper = new PhpDumper($container);
        file_put_contents(
            __DIR__ . '/../DI.php',
            $dumper->dump(['class' => 'DI', 'namespace' => 'VerteXVaaR\\BlueContainer'])
        );

        $this->io->write('Generated container');
    }

    private function loadServices(
        ContainerBuilder $container,
        PackageInterface $package,
        string $installPath,
    ): void {
        $packageExtra = $package->getExtra();
        $servicesPath = $packageExtra['vertexvaar/bluecontainer']['svc'] ?? null;

        if (null === $servicesPath) {
            $this->io->write(
                sprintf('Package %s does not define extra.vertexvaar/bluecontainer.svc, skipping', $package->getName()),
                true,
                IOInterface::VERY_VERBOSE
            );
            return;
        }

        $absoluteServicesPath = concat_paths($installPath, $servicesPath);
        if (!is_dir($absoluteServicesPath)) {
            $this->io->writeError(
                sprintf(
                    'Package %s defines extra.vertexvaar/bluecontainer.svc, but the directory "%s" does not exist',
                    $package->getName(),
                    $absoluteServicesPath
                )
            );
            return;
        }

        if (file_exists(concat_paths($absoluteServicesPath, 'services.yaml'))) {
            $this->io->write(
                sprintf('Loading services.yaml from package %s', $package->getName()),
                true,
                IOInterface::VERBOSE
            );
            $loader = new YamlFileLoader($container, new FileLocator($absoluteServicesPath));
            $loader->load('services.yaml');
        }
        if (file_exists(concat_paths($absoluteServicesPath, 'services.php'))) {
            $this->io->write(
                sprintf('Loading services.php from package %s', $package->getName()),
                true,
                IOInterface::VERBOSE
            );
            $loader = new PhpFileLoader($container, new FileLocator($absoluteServicesPath));
            $loader->load('services.php');
        }
    }
}
