<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig;

use function CoStack\Lib\array_value;

readonly class Config
{
    public function __construct(private array $config)
    {
    }

    public function get(string $path = ''): mixed
    {
        return array_value($this->config, $path);
    }
}
