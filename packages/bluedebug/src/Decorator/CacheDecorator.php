<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Decorator;

use DateInterval;
use Psr\SimpleCache\CacheInterface;
use VerteXVaaR\BlueDebug\Collector\CacheStatisticsCollector;

use function is_callable;

class CacheDecorator implements CacheInterface
{
    public function __construct(
        private readonly CacheInterface $inner,
        private readonly CacheStatisticsCollector $cacheStatisticsCollector,
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $hit = true;
        $wrapper = function () use ($default, &$hit) {
            $hit = false;
            if (is_callable($default)) {
                $default = $default();
            }
            return $default;
        };
        $value = $this->inner->get($key, $wrapper);
        $this->cacheStatisticsCollector->recordCall($key, $hit);
        return $value;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        return $this->inner->set($key, $value, $ttl);
    }

    public function delete(string $key): bool
    {
        return $this->inner->delete($key);
    }

    public function clear(): bool
    {
        return $this->inner->clear();
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        return $this->inner->getMultiple($keys, $default);
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        return $this->inner->setMultiple($values, $ttl);
    }

    public function deleteMultiple(iterable $keys): bool
    {
        return $this->inner->deleteMultiple($keys);
    }

    public function has(string $key): bool
    {
        $has = $this->inner->has($key);
        $this->cacheStatisticsCollector->recordCall($key, $has);
        return $has;
    }
}
