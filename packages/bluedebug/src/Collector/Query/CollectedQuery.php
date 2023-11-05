<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector\Query;

use function count;
use function hash;
use function json_encode;

class CollectedQuery
{
    /** @var array<QueryExecution> */
    private array $executions = [];

    public function __construct(
        public readonly string $query,
        public readonly array $context,
    ) {
    }

    public function getHash(): string
    {
        return hash('sha1', json_encode([$this->query, $this->context], JSON_THROW_ON_ERROR));
    }

    public function addExecution(QueryExecution $queryExecution): void
    {
        $this->executions[] = $queryExecution;
    }

    public function getCount(): int
    {
        return count($this->executions);
    }

    public function getCumulatedDuration(): int
    {
        $duration = 0;
        foreach ($this->executions as $execution) {
            $duration += $execution->getDuration();
        }
        return $duration;
    }
}
