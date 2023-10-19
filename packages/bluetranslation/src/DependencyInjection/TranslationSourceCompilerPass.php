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
use VerteXVaaR\BlueContainer\Generated\PackageExtras;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
use VerteXVaaR\BlueTranslation\TranslateTwigExtension;
use VerteXVaaR\BlueTranslation\TranslatorFactory;
use VerteXVaaR\BlueWeb\Template\TwigFactory;

use function array_key_exists;
use function array_keys;
use function explode;
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

        /** @var PackageIterator $packageIterator */
        $packageIterator = $container->get('package_iterator');
        $translations = $packageIterator->map(
            fn(Package $package) => $this->getTranslationResources($package, $container),
        );

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

    private function getTranslationResources(PackageInterface $package, ContainerBuilder $container): array
    {
        /** @var IOInterface $io */
        $io = $container->get('io');
        /** @var PackageExtras $packageExtras */
        $packageExtras = $container->get(PackageExtras::class);

        $packageName = $package->getName();
        $absoluteTranslationsPath = $packageExtras->getPath($packageName, 'translations');

        if (null === $absoluteTranslationsPath) {
            return [];
        }
        $recursiveDirectoryIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $absoluteTranslationsPath,
                FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS,
            ),
        );
        $translations = [];
        /** @var SplFileInfo $file */
        foreach ($recursiveDirectoryIterator as $file) {
            [$catalogue, $language] = explode('.', $file->getBasename());
            $pathname = $file->getPathname();
            $io->write(
                sprintf('Found translation resource "%s', $pathname),
                true,
                IOInterface::VERBOSE,
            );
            $translations[$file->getExtension()][$catalogue][$language][] = $pathname;
        }
        return $translations;
    }
}
