<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueTranslation\DependencyInjection;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Package\PackageInterface;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
use VerteXVaaR\BlueSprints\Template\TwigFactory;
use VerteXVaaR\BlueTranslation\TranslateTwigExtension;
use VerteXVaaR\BlueTranslation\TranslatorFactory;

use function array_key_exists;
use function array_keys;
use function array_merge;
use function CoStack\Lib\concat_paths;
use function explode;
use function getenv;
use function sprintf;

class TranslationSourceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        /** @var Composer $composer */
        $composer = $container->get('composer');
        /** @var IOInterface $io */
        $io = $container->get('io');

        $twigFactoryDefinition = $container->getDefinition(TwigFactory::class);
        $extensions = $twigFactoryDefinition->getArgument('$extensions');
        $extensions['translation'] = new Reference(TranslateTwigExtension::class);
        $twigFactoryDefinition->setArgument('$extensions', $extensions);

        $io->write('Loading translation resources', true, IOInterface::VERBOSE);

        $packageIterator = new PackageIterator($composer);
        $translations = $packageIterator->iterate(
            fn(Package $package, string $installPath) => $this->getTranslationResources($package, $installPath, $io)
        );
        $rootTranslations = $this->getTranslationResources($composer->getPackage(), getenv('VXVR_BS_ROOT'), $io);
        $translations = array_merge($rootTranslations, ...$translations);

        $definition = $container->getDefinition(TranslatorFactory::class);
        $loaders = $definition->getArgument('$loader');

        foreach (array_keys($translations) as $loader) {
            if (!array_key_exists($loader, $loaders)) {
                $io->writeError('Missing translation loader for ' . $loader . '. Removing resources!');
                unset($translations[$loader]);
            }
        }

        $definition->setArgument('$resources', $translations);

        $io->write('Loaded translation resources', true, IOInterface::VERBOSE);
    }

    private function getTranslationResources(PackageInterface $package, string $installPath, IOInterface $io): array
    {
        $extra = $package->getExtra();
        if (!isset($extra['vertexvaar/bluesprints']['translations'])) {
            return [];
        }
        $absolutePath = concat_paths($installPath, $extra['vertexvaar/bluesprints']['translations']);
        $recursiveDirectoryIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $absolutePath,
                FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS
            )
        );
        $translations = [];
        /** @var SplFileInfo $file */
        foreach ($recursiveDirectoryIterator as $file) {
            [$catalogue, $language] = explode('.', $file->getBasename());
            $pathname = $file->getPathname();
            $io->write(
                sprintf('Found translation resource "%s', $pathname),
                true,
                IOInterface::VERBOSE
            );
            $translations[$file->getExtension()][$catalogue][$language] = $pathname;
        }
        return $translations;
    }
}
