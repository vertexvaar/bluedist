<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Decorator;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

use function array_column;
use function array_sum;
use function is_callable;

class CacheDecorator implements CacheInterface
{
    protected static array $calls = [
        'get' => [],
        'has' => [],
    ];

    public function __construct(
        private readonly CacheInterface $inner,
    ) {
    }

    public static function getCalls(): array
    {
        return self::$calls + [
                'sumHits' => array_sum(array_column(self::$calls['get'], 'hits'))
                    + array_sum(array_column(self::$calls['has'], 'hits')),
                'sumMisses' => array_sum(array_column(self::$calls['get'], 'misses'))
                    + array_sum(array_column(self::$calls['has'], 'misses')),
            ];
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
        self::$calls['get'][$key]['hits'] ??= 0;
        self::$calls['get'][$key]['misses'] ??= 0;
        self::$calls['get'][$key][$hit ? 'hits' : 'misses']++;
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
        self::$calls['has'][$key]['hits'] ??= 0;
        self::$calls['has'][$key]['misses'] ??= 0;
        self::$calls['has'][$key][$has ? 'hits' : 'misses']++;
        return $has;
    }
}
