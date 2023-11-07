<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig\Factory;

use VerteXVaaR\BlueConfig\Config;
use VerteXVaaR\BlueConfig\Provider\Provider;

use function array_replace_recursive;

readonly class ConfigFactory
{
    /**
     * @param array<Provider> $providers
     */
    public function __construct(
        private iterable $providers,
    ) {
    }

    public function build(): Config
    {
        $config = [];
        foreach ($this->providers as $provider) {
            $config[] = $provider->get();
        }
        return new Config(array_replace_recursive([], ...$config));
    }
}
