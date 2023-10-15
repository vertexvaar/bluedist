<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Translation;

use Locale;
use Symfony\Component\Translation\Translator;
use VerteXVaaR\BlueSprints\Environment\Context;
use VerteXVaaR\BlueSprints\Environment\Environment;
use VerteXVaaR\BlueSprints\Environment\Paths;

use function CoStack\Lib\concat_paths;
use function getenv;

readonly class TranslatorFactory
{
    public function __construct(
        private Paths $paths,
        private Environment $environment,
        private array $resources,
        private array $loader,
    ) {
    }

    public function create(): Translator
    {
        $locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en');
        $translator = new Translator(
            $locale,
            null,
            concat_paths(getenv('VXVR_BS_ROOT'), $this->paths->cache, 'translations'),
            $this->environment->context === Context::Development
        );
        foreach ($this->resources as $loader => $domains) {
            $translator->addLoader($loader, $this->loader[$loader]);
            foreach ($domains as $domain => $languages) {
                foreach ($languages as $language => $source) {
                    $translator->addResource($loader, $source, $language, $domain);
                }
            }
        }

        return $translator;
    }
}
