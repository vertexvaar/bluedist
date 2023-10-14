<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Template\DependencyInjection;

use Composer\Composer;
use Composer\Package\Package;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Twig\Loader\FilesystemLoader;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
use VerteXVaaR\BlueSprints\Template\TemplatePathsRegistry;

use function array_filter;
use function array_replace;
use function CoStack\Lib\concat_paths;
use function getenv;
use function strtr;

class TemplateRendererCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        /** @var Composer $composer */
        $composer = $container->get('composer');
        $packageIterator = new PackageIterator($composer);
        $templatePaths = $packageIterator->iterate(static function (Package $package, string $installPath): ?array {
            $extra = $package->getExtra();
            if (isset($extra['vertexvaar/bluesprints']['view'])) {
                $viewPath = $extra['vertexvaar/bluesprints']['view'];
                $fullViewPath = concat_paths($installPath, $viewPath);
                return [
                    strtr($package->getName(), '/', '_') => $fullViewPath
                ];
            }
            return null;
        });


        $rootPaths = [];
        $extra = $composer->getPackage()->getExtra();
        if (isset($extra['vertexvaar/bluesprints']['view'])) {
            $viewPath = $extra['vertexvaar/bluesprints']['view'];
            $fullViewPath = concat_paths(getenv('VXVR_BS_ROOT'), $viewPath);
            $rootPaths[FilesystemLoader::MAIN_NAMESPACE] = $fullViewPath;
        }
        $templatePaths = array_replace($rootPaths, ...array_filter($templatePaths));

        $definition = $container->getDefinition(TemplatePathsRegistry::class);
        $definition->setArgument('$paths', $templatePaths);
    }
}
