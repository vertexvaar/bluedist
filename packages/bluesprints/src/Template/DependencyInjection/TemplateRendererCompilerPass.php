<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Template\DependencyInjection;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\Package;
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
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
use VerteXVaaR\BlueSprints\Environment\Context;
use VerteXVaaR\BlueSprints\Template\TemplatePathsRegistry;

use function array_filter;
use function array_replace;
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
        /** @var Composer $composer */
        $composer = $container->get('composer');
        /** @var IOInterface $io */
        $io = $container->get('io');

        $io->write('Loading templates', true, IOInterface::VERBOSE);

        $packageIterator = new PackageIterator($composer);
        $templatePaths = $packageIterator->iterate(
            static function (Package $package, string $installPath) use ($io): ?array {
                $extra = $package->getExtra();
                if (isset($extra['vertexvaar/bluesprints']['view'])) {
                    $viewPath = $extra['vertexvaar/bluesprints']['view'];
                    $fullViewPath = concat_paths($installPath, $viewPath);
                    $namespace = strtr($package->getName(), '/', '_');
                    $io->write(
                        sprintf(
                            'Identified templates root %s for namespace %s',
                            $fullViewPath,
                            $namespace
                        ),
                        true,
                        IOInterface::VERBOSE
                    );
                    return [
                        $namespace => $fullViewPath
                    ];
                }
                return null;
            }
        );


        $rootPaths = [];
        $extra = $composer->getPackage()->getExtra();
        if (isset($extra['vertexvaar/bluesprints']['view'])) {
            $viewPath = $extra['vertexvaar/bluesprints']['view'];
            $fullViewPath = concat_paths(getenv('VXVR_BS_ROOT'), $viewPath);
            $rootPaths[FilesystemLoader::MAIN_NAMESPACE] = $fullViewPath;
            $io->write(
                sprintf(
                    'Identified templates root %s for namespace %s',
                    $fullViewPath,
                    FilesystemLoader::MAIN_NAMESPACE
                ),
                true,
                IOInterface::VERBOSE
            );
        }
        $templatePaths = array_replace($rootPaths, ...array_filter($templatePaths));

        $definition = $container->getDefinition(TemplatePathsRegistry::class);
        $definition->setArgument('$paths', $templatePaths);

        $io->write('Loaded templates', true, IOInterface::VERBOSE);

        $io->write('Warming template caches', true, IOInterface::VERBOSE);

        $loader = new FilesystemLoader();
        foreach ($templatePaths as $namespace => $path) {
            $loader->addPath($path, $namespace);
        }

        $context = Context::fromString((string)getenv('VXVR_BS_CONTEXT'));
        $packageConfig = $composer->getPackage()->getExtra();
        $config = $packageConfig['vertexvaar/bluesprints'];
        $twigCachePath = concat_paths(getenv('VXVR_BS_ROOT'), $config['cache'] ?? 'cache', 'twig');
        $filesystemCache = new FilesystemCache($twigCachePath);
        $twig = new View(
            $loader,
            [
                'cache' => $filesystemCache,
                'debug' => $context === Context::Development
            ]
        );
        if ($context === Context::Development) {
            $twig->addExtension(new DebugExtension());
        }
        // The actual translator is not required as twig compiles with runtime getter
        $translator = new Translator('en_GB');

        $twig->addFilter(new TwigFilter('t', $translator->trans(...)));

        $twigTemplatesPath = concat_paths(getenv('VXVR_BS_ROOT'), $config['view'] ?? 'view');

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($twigTemplatesPath, FilesystemIterator::SKIP_DOTS)
        );

        /** @var IOInterface $io */
        $io = $container->get('io');
        $prefixLength = strlen($twigTemplatesPath);
        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            $file = substr($file->getPathname(), $prefixLength);
            $io->write(sprintf("Warming template cache for file %s", $file), true, IOInterface::VERBOSE);
            $twig->load($file);
        }

        $io->write('Warmed template caches', true, IOInterface::VERBOSE);
    }
}
