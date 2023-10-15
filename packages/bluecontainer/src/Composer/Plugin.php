<?php

namespace VerteXVaaR\BlueContainer\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Package\Package;
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

use function dirname;
use function file_exists;
use function getcwd;
use function getenv;
use function is_dir;
use function putenv;
use function xdebug_break;

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
        $this->io->write(sprintf("VXVR_BS_ROOT=%s", getenv('VXVR_BS_ROOT')));

        $installationManager = $this->composer->getInstallationManager();
        $config = $this->composer->getConfig();
        $autoloadFile = $config->get('vendor-dir') . '/autoload.php';
        if (!file_exists($autoloadFile)) {
            return;
        }
        require $autoloadFile;

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->set('composer', $this->composer);
        $containerBuilder->set('io', $this->io);
        $diDefinition = new Definition(DI::class);
        $diDefinition->setPublic(true);
        $diDefinition->setShared(true);
        $containerBuilder->setDefinition(ContainerInterface::class, $diDefinition);

        $packageIterator = new PackageIterator($this->composer);
        $packageIterator->iterate(
            function (Package $package, string $installPath) use ($containerBuilder): void {
                $configPath = $installPath . '/config';
                if (file_exists($configPath) && is_dir($configPath)) {
                    if (file_exists($configPath . '/services.yaml')) {
                        $this->io->write(
                            'Loading services.yaml from ' . $package->getName(),
                            true,
                            IOInterface::VERBOSE
                        );
                        $loader = new YamlFileLoader($containerBuilder, new FileLocator($configPath));
                        $loader->load('services.yaml');
                    }
                    if (file_exists($configPath . '/services.php')) {
                        $this->io->write(
                            'Loading services.php from ' . $package->getName(),
                            true,
                            IOInterface::VERBOSE
                        );
                        $loader = new PhpFileLoader($containerBuilder, new FileLocator($configPath));
                        $loader->load('services.php');
                    }
                }
            }
        );


        $packageConfig = $this->composer->getPackage()->getExtra();
        $configPath = $packageConfig['vertexvaar/bluesprints']['config'] ?? 'config';

        if (file_exists($configPath . '/services.yaml')) {
            $loader = new YamlFileLoader($containerBuilder, new FileLocator($configPath));
            $loader->load('services.yaml');
        }
        if (file_exists($configPath . '/services.php')) {
            $loader = new PhpFileLoader($containerBuilder, new FileLocator($configPath));
            $loader->load('services.php');
        }

        $containerBuilder->compile();

        $dumper = new PhpDumper($containerBuilder);
        file_put_contents(
            __DIR__ . '/../DI.php',
            $dumper->dump(['class' => 'DI', 'namespace' => 'VerteXVaaR\\BlueContainer'])
        );

        $this->io->write('Generated container');
    }
}
