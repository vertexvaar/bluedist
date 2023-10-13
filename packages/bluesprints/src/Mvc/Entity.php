<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

abstract class Entity
{
    public function __construct(public readonly string $uuid)
    {
    }
}
