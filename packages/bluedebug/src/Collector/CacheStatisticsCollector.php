<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector;

use VerteXVaaR\BlueDebug\CollectorRendering;

use function array_sum;
use function CoStack\Lib\array_unique_keys;

class CacheStatisticsCollector implements Collector
{
    private array $calls = [];

    public function recordHit(string $key): void
    {
        $this->calls['hit'][$key] ??= 0;
        $this->calls['hit'][$key]++;
    }

    public function recordMiss($key): void
    {
        $this->calls['miss'][$key] ??= 0;
        $this->calls['miss'][$key]++;
    }

    public function recordCall(string $key, bool $hit): void
    {
        if ($hit) {
            $this->recordHit($key);
            return;
        }
        $this->recordMiss($key);
    }

    public function render(): CollectorRendering
    {
        $table = [];
        foreach (array_unique_keys($this->calls['hit'] ?? [], $this->calls['miss'] ?? []) as $key) {
            $table[$key] = ($this->calls['hit'][$key] ?? 0) . '/' . ($this->calls['miss'][$key] ?? 0);
        }
        return new CollectorRendering(
            'Cache',
            'hit/miss ' . array_sum($this->calls['hit'] ?? []) . '/' . array_sum($this->calls['miss'] ?? []),
            $table,
        );
    }
}
