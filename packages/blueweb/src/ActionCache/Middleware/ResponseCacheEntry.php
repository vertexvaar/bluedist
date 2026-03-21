<?php

namespace VerteXVaaR\BlueWeb\ActionCache\Middleware;

readonly class ResponseCacheEntry
{
    public function __construct(
        public string $contents,
        public array $headers,
    ) {}
}
