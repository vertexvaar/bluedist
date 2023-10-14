<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Scheduler\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class ScheduledTask
{
    public function __construct(public string $identifier, public int $interval, public array $config)
    {
    }

}
