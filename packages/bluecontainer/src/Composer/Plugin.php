<?php

namespace VerteXVaaR\BlueContainer\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueContainer\Composer\Steps\CompileDependencyInjectionContainer;
use VerteXVaaR\BlueContainer\Composer\Steps\CompilePackageExtras;
use VerteXVaaR\BlueContainer\Composer\Steps\CreatePackageIteratorService;
use VerteXVaaR\BlueContainer\Composer\Steps\RequireLocalAutoloader;
use VerteXVaaR\BlueContainer\Composer\Steps\Step;

use function getcwd;
use function getenv;
use function putenv;
use function sprintf;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private Composer $composer;
    private IOInterface $io;
    /**
     * @var array<class-string<Step>>
     */
    private array $steps = [
        RequireLocalAutoloader::class,
        CreatePackageIteratorService::class,
        CompilePackageExtras::class,
        CompileDependencyInjectionContainer::class,
    ];

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

        $container = new ContainerBuilder();
        $container->set('composer', $this->composer);
        $container->set('io', $this->io);
        $container->setAlias(ContainerInterface::class, 'service_container');

        /** @var Step $step */
        foreach ($this->steps as $step) {
            $step = new $step;
            $step->run($container);
        }
    }
}
