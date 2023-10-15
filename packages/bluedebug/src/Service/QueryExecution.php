<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Service;

class QueryExecution
{
    public function __construct(
        public readonly int $start,
        public readonly int $stop,
    ) {
    }

    public function getDuration(): int
    {
        return $this->stop - $this->start;
    }
}
