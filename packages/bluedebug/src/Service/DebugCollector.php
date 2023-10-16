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

    public function add(string $key): int
    {
        if (!isset($this->items[$key])) {
            $this->items[$key] = 0;
        }
        return $this->items[$key]++;
    }

    public function getItem(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }
}
