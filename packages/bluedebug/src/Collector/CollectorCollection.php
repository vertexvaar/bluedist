<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector;

use VerteXVaaR\BlueDebug\Rendering\CollectorRendering;

class CollectorCollection
{
    /** @var array<Collector> */
    private array $collectors = [];

    public function addCollector(Collector $collector): void
    {
        $this->collectors[] = $collector;
    }

    /**
     * @return iterable<CollectorRendering>
     */
    public function render(): iterable
    {
        foreach ($this->collectors as $collector) {
            yield $collector->render();
        }
    }
}
