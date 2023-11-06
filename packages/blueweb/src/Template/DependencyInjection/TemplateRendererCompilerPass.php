<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Template\DependencyInjection;

use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Package\RootPackageInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Twig\Loader\FilesystemLoader;
use VerteXVaaR\BlueContainer\Generated\PackageExtras;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
use VerteXVaaR\BlueWeb\Template\TwigFactory;

use function sprintf;
use function strtr;

class TemplateRendererCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $io = $container->get('io');
        $packageExtras = $container->get(PackageExtras::class);

        $io->write('Loading templates', true, IOInterface::VERBOSE);

        /** @var PackageIterator $packageIterator */
        $packageIterator = $container->get('package_iterator');
        $templatePaths = $packageIterator->map(
            static function (Package $package) use ($io, $packageExtras): ?array {
                $packageName = $package->getName();
                $absoluteViewPath = $packageExtras->getPath($packageName, 'view');

                if (null !== $absoluteViewPath) {
                    if ($package instanceof RootPackageInterface) {
                        $namespace = FilesystemLoader::MAIN_NAMESPACE;
                    } else {
                        $namespace = strtr($packageName, '/', '_');
                    }
                    $io->write(
                        sprintf(
                            'Identified templates root %s for namespace %s',
                            $absoluteViewPath,
                            $namespace,
                        ),
                        true,
                        IOInterface::VERBOSE,
                    );
                    return [
                        $namespace => $absoluteViewPath,
                    ];
                }
                return null;
            },
        );

        $definition = $container->getDefinition(TwigFactory::class);
        $definition->setArgument('$templatePaths', $templatePaths);

        $io->write('Loaded templates', true, IOInterface::VERBOSE);
    }
}
