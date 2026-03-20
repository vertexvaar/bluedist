<?php

namespace VerteXVaaR\BlueWeb\FlashMessage;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

class FlashMessageStore
{
    public function __construct(protected readonly CacheInterface $cache) {}

    public function store(string|int $for, FlashMessage $flashMessage): void
    {
        $flashMessages = $this->cache->get('flashMessage.' . $for, []);
        $flashMessages[] = $flashMessage;
        $this->cache->set('flashMessage.' . $for, $flashMessages, new DateInterval('PT1M'));
    }

    /**
     * @return array<FlashMessage>
     */
    public function get(string|int $for): array
    {
        $flashMessages = $this->cache->get('flashMessage.' . $for, []);
        $this->cache->delete('flashMessage.' . $for);
        return $flashMessages;
    }
}
