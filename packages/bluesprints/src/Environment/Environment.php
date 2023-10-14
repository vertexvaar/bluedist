<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Environment;

final readonly class Environment
{
    public function __construct(public Context $context)
    {
    }
}
