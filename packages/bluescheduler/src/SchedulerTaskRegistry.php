<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueScheduler;

readonly class SchedulerTaskRegistry
{
    public function __construct(
        public array $tasks,
    ) {}
}
