<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Template;

use Twig\Cache\FilesystemCache;
use Twig\Environment as View;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use VerteXVaaR\BlueSprints\Environment\Context;
use VerteXVaaR\BlueSprints\Environment\Environment;
use VerteXVaaR\BlueSprints\Environment\Paths;

use function CoStack\Lib\concat_paths;
use function getenv;

readonly class TwigFactory
{
    public function __construct(
        private TemplatePathsRegistry $templatePathsRegistry,
        private Paths $paths,
        private Environment $environment,
        private array $extensions
    ) {
    }

    public function create(): View
    {
        $loader = new FilesystemLoader();
        foreach ($this->templatePathsRegistry->paths as $namespace => $path) {
            $loader->addPath($path, $namespace);
        }
        $twigCachePath = concat_paths(getenv('VXVR_BS_ROOT'), $this->paths->cache, 'twig');
        $filesystemCache = new FilesystemCache($twigCachePath);
        $twig = new View(
            $loader,
            [
                'cache' => $filesystemCache,
                'debug' => $this->environment->context === Context::Development
            ]
        );
        foreach ($this->extensions as $extension) {
            $twig->addExtension($extension);
        }
        if ($this->environment->context === Context::Development) {
            $twig->addExtension(new DebugExtension());
        }
        return $twig;
    }
}
