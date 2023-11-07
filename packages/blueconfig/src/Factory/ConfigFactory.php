<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig\Factory;

use VerteXVaaR\BlueConfig\Config;
use VerteXVaaR\BlueConfig\Definition\DefinitionService;
use VerteXVaaR\BlueConfig\Provider\Provider;

use function array_replace_recursive;

readonly class ConfigFactory
{
    /**
     * @param array<Provider> $providers
     */
    public function __construct(
        private iterable $providers,
        private DefinitionService $definitionService,
    ) {
    }

    public function build(): Config
    {
        $config = [];
        foreach ($this->providers as $provider) {
            $config[] = $provider->get();
        }
        $config = array_replace_recursive([], ...$config);
        $this->definitionService->cast($config);
        return new Config($config);
    }
}
