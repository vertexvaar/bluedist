<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConsole\DependencyInjection;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VerteXVaaR\BlueConsole\CommandRegistry;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;

use function array_merge;
use function CoStack\Lib\concat_paths;
use function file_exists;
use function getenv;
use function sprintf;

class CommandCollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        /** @var Composer $composer */
        $composer = $container->get('composer');
        /** @var IOInterface $io */
        $io = $container->get('io');

        $io->write('Loading commands', true, IOInterface::VERBOSE);

        $packageIterator = new PackageIterator($composer);
        $commands = $packageIterator->iterate(
            fn(PackageInterface $package, string $installPath) => $this->loadCommands($package, $installPath, $io)
        );

        $packageCommands = $this->loadCommands($composer->getPackage(), getenv('VXVR_BS_ROOT'), $io);

        $commands = array_merge($packageCommands, ...$commands);

        foreach ($commands as $index => $command) {
            $commands[$index] = new Reference($command);
        }

        $registry = $container->getDefinition(CommandRegistry::class);
        $registry->setArgument('$commands', $commands);

        $io->write('Loaded commands', true, IOInterface::VERBOSE);
    }

    private function loadCommands(PackageInterface $package, string $installPath, IOInterface $io): array
    {
        $extra = $package->getExtra();
        $name = $package->getName();
        if (!isset($extra['vertexvaar/blueconsole']['config'])) {
            $io->write(
                sprintf('Package %s does not define extra.vertexvaar/blueconsole.config, skipping', $name),
                true,
                IOInterface::VERY_VERBOSE
            );
            return [];
        }

        $absoluteCommandsPath = concat_paths($installPath, $extra['vertexvaar/blueconsole']['config'], 'commands.php');
        if (!file_exists($absoluteCommandsPath)) {
            $io->write(
                sprintf(
                    'Package %s defines extra.vertexvaar/blueconsole.config, but the file commands.php does not exist',
                    $name
                ),
                true,
                IOInterface::VERBOSE
            );
            return [];
        }

        $io->write(sprintf('Found commands.php in package %s', $name), true, IOInterface::VERBOSE);

        return require $absoluteCommandsPath;
    }
}
