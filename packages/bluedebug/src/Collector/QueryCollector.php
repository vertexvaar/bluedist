<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector;

use VerteXVaaR\BlueDebug\Collector\Query\CollectedQuery;
use VerteXVaaR\BlueDebug\Collector\Query\QueryExecution;
use VerteXVaaR\BlueDebug\CollectorRendering;

use function hrtime;
use function implode;

class QueryCollector implements Collector
{
    /** @var array<CollectedQuery> */
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

    public function render(): CollectorRendering
    {
        $count = 0;
        foreach ($this->queries as $query) {
            $count += $query->getCount();
        }

        $table = [];
        foreach ($this->queries as $query) {
            $table[$this->queryToTableKey($query)] = $query->getCount()
                . ' (' . ($query->getCumulatedDuration() / 1000000) . 'ms)';
        }

        return new CollectorRendering(
            'Queries',
            (string)$count,
            $table,
        );
    }

    protected function queryToTableKey(CollectedQuery $query): string
    {
        $string = $query->query;
        $contextStrings = [];
        foreach ($query->context as $name => $value) {
            $contextStrings[] = $name . '="' . $value . '"';
        }
        return $string . ' ' . implode(', ', $contextStrings);
    }
}
