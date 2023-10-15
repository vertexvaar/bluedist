<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Service;

class DebugCollector
{
    private array $items = [];

    public function collect(string $key, mixed $value)
    {
        $this->items[$key] = $value;
    }

    public function getItem(string $key): mixed
    {
        return $this->items[$key];
    }
}
