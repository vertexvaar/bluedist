<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Template\DependencyInjection;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Twig\Loader\FilesystemLoader;
use VerteXVaaR\BlueFoundation\PackageExtras;
use VerteXVaaR\BlueWeb\Template\TwigFactory;

use function array_merge;
use function array_values;
use function is_array;
use function is_string;
use function krsort;
use function sprintf;
use function str_replace;

class TemplateRendererCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var OutputInterface $output */
        $output = $container->get('_output');

        $output->writeln('Loading templates', OutputInterface::VERBOSITY_VERBOSE);

        $packageExtras = $container->get(PackageExtras::class);

        $templatePaths = [];
        foreach ($packageExtras->getPackageNames() as $packageName) {
            $viewConfiguration = $packageExtras->getPath($packageName, 'view');

            if (null === $viewConfiguration) {
                $output->writeln(
                    sprintf(
                        'No view configuration in package "%s"',
                        $packageName,
                    ),
                    OutputInterface::VERBOSITY_VERY_VERBOSE,
                );
                continue;
            }

            if (is_string($viewConfiguration)) {
                if ($packageExtras->rootPackageName === $packageName) {
                    $namespace = FilesystemLoader::MAIN_NAMESPACE;
                } else {
                    $namespace = str_replace('/', '_', $packageName);
                }
                $viewConfiguration = [
                    $namespace => $viewConfiguration,
                ];
            }

            if (!is_array($viewConfiguration)) {
                $output->writeln(
                    sprintf(
                        'Invalid view configuration in package "%s"',
                        $packageName,
                    ),
                );
                continue;
            }

            $found = 0;

            foreach ($viewConfiguration as $namespace => $namespacePaths) {
                foreach ((array)$namespacePaths as $index => $namespacePath) {
                    $templatePaths[$namespace][$index][] = $namespacePath;
                    ++$found;

                    $output->writeln(
                        sprintf(
                            '  - Adding view namespace "%s" path "%s" from package "%s"',
                            $namespace,
                            $namespacePath,
                            $packageName,
                        ),
                        OutputInterface::VERBOSITY_DEBUG,
                    );
                }
            }

            $output->writeln(
                sprintf(
                    'Found %d view paths in package "%s"',
                    $found,
                    $packageName,
                ),
                OutputInterface::VERBOSITY_VERBOSE,
            );
        }

        foreach ($templatePaths as $namespace => $indexedPaths) {
            krsort($indexedPaths);
            $templatePaths[$namespace] = array_merge([], ...array_values($indexedPaths));
        }

        $definition = $container->getDefinition(TwigFactory::class);
        $definition->setArgument('$templatePaths', $templatePaths);

        $output->writeln('Loaded templates', OutputInterface::VERBOSITY_VERBOSE);
    }
}
