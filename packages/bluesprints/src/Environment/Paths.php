<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Environment;

readonly class Paths
{
    public function __construct(
        public string $logs,
        public string $locks,
        public string $cache,
        public string $database,
        public string $config,
        public string $view,
        public string $translations,
    ) {
    }
}
