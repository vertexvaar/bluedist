<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Template\DependencyInjection;

use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Package\RootPackageInterface;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Translation\Translator;
use Twig\Cache\FilesystemCache;
use Twig\Environment as View;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use VerteXVaaR\BlueContainer\Generated\PackageExtras;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
use VerteXVaaR\BlueSprints\Environment\Context;
use VerteXVaaR\BlueWeb\Template\TwigFactory;

use function CoStack\Lib\concat_paths;
use function getenv;
use function sprintf;
use function strlen;
use function strtr;
use function substr;

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
        $extensions = $definition->getArgument('$extensions');
        $definition->setArgument('$templatePaths', $templatePaths);

        $io->write('Loaded templates', true, IOInterface::VERBOSE);

        $io->write('Warming template caches', true, IOInterface::VERBOSE);

        $loader = new FilesystemLoader();
        foreach ($templatePaths as $namespace => $path) {
            $loader->addPath($path, $namespace);
        }

        $context = Context::fromString((string)getenv('VXVR_BS_CONTEXT'));

        $absoluteCachePath = $packageExtras->getPath($packageExtras->rootPackageName, 'cache')
            ?? concat_paths(getenv('VXVR_BS_ROOT'), 'var/cache',);
        $twigCachePath = concat_paths($absoluteCachePath, 'twig');

        $filesystemCache = new FilesystemCache($twigCachePath);
        $twig = new View(
            $loader,
            [
                'cache' => $filesystemCache,
                'debug' => $context === Context::Development,
            ],
        );
        if ($context === Context::Development) {
            $twig->addExtension(new DebugExtension());
        }
        foreach ($extensions as $extension) {
            $twig->addExtension($container->resolveServices($extension));
        }
        // The actual translator is not required as twig compiles with runtime getter
        $translator = new Translator('en_GB');

        $twig->addFilter(new TwigFilter('t', $translator->trans(...)));

        foreach ($templatePaths as $namespace => $path) {
            $prefix = '';
            if (FilesystemLoader::MAIN_NAMESPACE !== $namespace) {
                $prefix = '@' . $namespace . '/';
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            );

            $prefixLength = strlen($path) + 1;
            /** @var SplFileInfo $file */
            foreach ($iterator as $file) {
                $fileName = substr($file->getPathname(), $prefixLength);
                $io->write(
                    sprintf("Warming template cache for file %s%s", $prefix, $fileName),
                    true,
                    IOInterface::VERBOSE,
                );
                $twig->load($prefix . $fileName);
            }
        }

        $io->write('Warmed template caches', true, IOInterface::VERBOSE);
    }
}
