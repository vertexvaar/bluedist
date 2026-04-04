<?php

namespace VerteXVaaR\BlueWeb\FlashMessage;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

use function serialize;
use function unserialize;

readonly class FlashMessageStore
{
    public function __construct(protected CacheInterface $cache) {}

    public function store(string|int $for, FlashMessage $flashMessage, DateInterval|int|null $ttl = null): void
    {
        $key = $this->getKey($for);

        $flashMessages = $this->cache->get($key, []);
        $flashMessages[] = serialize($flashMessage);

        $this->cache->set($key, $flashMessages, $ttl ?? new DateInterval('PT1M'));
    }

    /**
     * @return array<FlashMessage>
     */
    public function get(string|int $for): array
    {
        $key = $this->getKey($for);

        $flashMessages = $this->cache->get($key, []);
        $this->cache->delete($key);

        foreach ($flashMessages as $index => $flashMessage) {
            $flashMessages[$index] = unserialize($flashMessage, ['allowed_classes' => [FlashMessage::class]]);
        }
        return $flashMessages;
    }

    protected function getKey(int|string $for): string
    {
        return 'flashMessage/' . $for;
    }
}
