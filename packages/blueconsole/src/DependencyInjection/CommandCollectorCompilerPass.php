<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConsole\DependencyInjection;

use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VerteXVaaR\BlueConsole\BlueApplication;
use VerteXVaaR\BlueContainer\Generated\PackageExtras;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;

use function CoStack\Lib\concat_paths;
use function file_exists;
use function sprintf;

class CommandCollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $io = $container->get('io');
        /** @var PackageIterator $packageIterator */
        $packageIterator = $container->get('package_iterator');

        $io->write('Loading commands', true, IOInterface::VERBOSE);

        $commands = $packageIterator->map(
            fn(PackageInterface $package): array => $this->loadCommands($package, $container),
        );

        foreach ($commands as $index => $command) {
            $commands[$index] = new Reference($command);
        }

        $blueApplication = $container->getDefinition(BlueApplication::class);
        $blueApplication->setArgument('$commands', $commands);

        $io->write('Loaded commands', true, IOInterface::VERBOSE);
    }

    private function loadCommands(PackageInterface $package, ContainerBuilder $container): array
    {
        $io = $container->get('io');
        $packageExtra = $container->get(PackageExtras::class);

        $packageName = $package->getName();

        $absoluteCommandsPath = $packageExtra->getPath($packageName, 'commands');

        if (null === $absoluteCommandsPath) {
            $io->write(
                sprintf('Package %s does not define extra.vertexvaar/bluesprints.commands, skipping', $packageName),
                true,
                IOInterface::VERY_VERBOSE,
            );
            return [];
        }

        $absoluteCommandsFile = concat_paths($absoluteCommandsPath, 'commands.php');
        if (!file_exists($absoluteCommandsFile)) {
            $io->write(
                sprintf(
                    'Package %s defines extra.vertexvaar/bluesprints.commands, but the file commands.php does not exist',
                    $packageName,
                ),
                true,
                IOInterface::VERBOSE,
            );
            return [];
        }

        $io->write(sprintf('Found commands.php in package %s', $packageName), true, IOInterface::VERBOSE);

        return require $absoluteCommandsFile;
    }
}
