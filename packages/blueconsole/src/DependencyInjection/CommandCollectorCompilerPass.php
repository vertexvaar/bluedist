<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConsole\DependencyInjection;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VerteXVaaR\BlueConsole\BlueApplication;
use VerteXVaaR\BlueContainer\Generated\PackageExtras;

use function CoStack\Lib\concat_paths;
use function file_exists;
use function sprintf;

class CommandCollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        /** @var OutputInterface $output */
        $output = $container->get('_output');

        $output->writeln('Loading commands', OutputInterface::VERBOSITY_VERBOSE);

        $commands = $this->loadCommands($container);

        $blueApplication = $container->getDefinition(BlueApplication::class);
        $blueApplication->setArgument('$commands', $commands);

        $output->writeln('Loaded commands', OutputInterface::VERBOSITY_VERBOSE);
    }

    private function loadCommands(ContainerBuilder $container): array
    {
        /** @var OutputInterface $output */
        $output = $container->get('_output');
        $packageExtras = $container->get(PackageExtras::class);

        $commands =[];
        foreach ($packageExtras->getPackageNames() as $packageName) {

            $absoluteCommandsPath = $packageExtras->getPath($packageName, 'commands');

            if (null === $absoluteCommandsPath) {
                $output->writeln(
                    sprintf('Package %s does not define extra.vertexvaar/bluesprints.commands, skipping', $packageName),
                    OutputInterface::VERBOSITY_VERY_VERBOSE,
                );
                continue;
            }

            $absoluteCommandsFile = concat_paths($absoluteCommandsPath, 'commands.php');
            if (!file_exists($absoluteCommandsFile)) {
                $output->writeln(
                    sprintf(
                        'Package %s defines extra.vertexvaar/bluesprints.commands, but the file commands.php does not exist',
                        $packageName,
                    ),
                    OutputInterface::VERBOSITY_VERBOSE,
                );
                continue;
            }

            $output->writeln(sprintf('Found commands.php in package %s', $packageName), OutputInterface::VERBOSITY_VERBOSE);

            $packageCommands = require $absoluteCommandsFile;
            foreach ($packageCommands as $index => $command) {
                $commands[] = new Reference($command);
            }
        }

        return $commands;
    }
}
