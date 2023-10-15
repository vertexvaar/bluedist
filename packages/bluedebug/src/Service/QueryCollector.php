<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Service;

use function hrtime;

class QueryCollector
{
    private array $queries = [];

    public function execute(string $query, array $context, callable $closure, array $arguments): mixed
    {
        $start = hrtime(true);
        $return = $closure(...$arguments);
        $stop = hrtime(true);
        $this->recordQuery($start, $stop, $query, $context);
        return $return;
    }

    private function recordQuery(int $start, int $end, string $query, array $context): void
    {
        $collectedQuery = new CollectedQuery($query, $context);
        $hash = $collectedQuery->getHash();
        if (!isset($this->queries[$hash])) {
            $this->queries[$hash] = $collectedQuery;
        }
        $this->queries[$hash]->addExecution(new QueryExecution($start, $end));
    }

    /**
     * @return array<CollectedQuery>
     */
    public function getQueries(): array
    {
        return $this->queries;
    }
}
