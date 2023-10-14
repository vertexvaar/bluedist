<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Routing\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Route
{
    public function __construct(public string $path, public string $method = 'GET', public int $priority = 100)
    {
    }
}
