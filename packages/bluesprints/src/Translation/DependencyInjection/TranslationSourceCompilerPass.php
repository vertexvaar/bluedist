<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Translation\DependencyInjection;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
use VerteXVaaR\BlueSprints\Translation\TranslatorFactory;

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

        $io->write('Loading translation resources', true, IOInterface::VERBOSE);

        $packageIterator = new PackageIterator($composer);
        $translations = $packageIterator->iterate(static function (Package $package, string $installPath) use ($io) {
            $translations = [];
            $extra = $package->getExtra();
            if (isset($extra['vertexvaar/bluesprints']['translations'])) {
                $translationsPath = concat_paths($installPath, $extra['vertexvaar/bluesprints']['translations']);
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($translationsPath),
                    FilesystemIterator::SKIP_DOTS
                );
                /** @var SplFileInfo $file */
                foreach ($iterator as $file) {
                    $fileName = $file->getBasename();
                    [$catalogue, $language] = explode('.', $fileName);
                    $loader = $file->getExtension();
                    $pathname = $file->getPathname();
                    $io->write(
                        sprintf('Found translation resource "%s', $pathname),
                        true,
                        IOInterface::VERBOSE
                    );
                    $translations[$loader][$catalogue][$language] = $pathname;
                }
            }
            return $translations;
        });

        $rootTranslations = [];
        $extra = $composer->getPackage()->getExtra();
        if (isset($extra['vertexvaar/bluesprints']['translations'])) {
            $translationsPath = concat_paths(getenv('VXVR_BS_ROOT'), $extra['vertexvaar/bluesprints']['translations']);
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $translationsPath,
                    FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS
                ),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            /** @var SplFileInfo $file */
            foreach ($iterator as $file) {
                $fileName = $file->getBasename();
                [$catalogue, $language] = explode('.', $fileName);
                $loader = $file->getExtension();
                $pathname = $file->getPathname();
                $io->write(
                    sprintf('Found translation resource "%s', $pathname),
                    true,
                    IOInterface::VERBOSE
                );
                $rootTranslations[$loader][$catalogue][$language] = $pathname;
            }
        }

        $phpFileLoader = new Definition(PhpFileLoader::class);
        $container->setDefinition(PhpFileLoader::class, $phpFileLoader);

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
}
