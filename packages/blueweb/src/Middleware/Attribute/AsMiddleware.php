<?php

namespace VerteXVaaR\BlueWeb\Middleware\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class AsMiddleware
{
    public function __construct(
        public string $name,
        public array $before = [],
        public array $after = [],
    ) {}
}
