<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Environment;

use function getenv;

final readonly class Environment
{
    public Context $context;

    public function __construct()
    {
        $this->context = Context::fromString((string)getenv('VXVR_BS_CONTEXT'));
    }
}
