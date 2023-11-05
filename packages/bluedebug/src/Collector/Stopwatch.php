<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector;

use Exception;
use VerteXVaaR\BlueDebug\CollectorRendering;

use function hrtime;
use function round;

class Stopwatch implements Collector
{
    private array $timings = [];

    public function start(string $name): void
    {
        $this->timings[$name]['start'] = hrtime(true);
    }

    public function stop(string $name): void
    {
        if (!isset($this->timings[$name])) {
            throw new Exception(sprintf("Timer not started for %s", $name));
        }
        $this->timings[$name]['end'] = hrtime(true);
        $this->timings[$name]['duration'] = $this->timings[$name]['end'] - $this->timings[$name]['start'];
    }

    public function render(): CollectorRendering
    {
        $timings = $this->timings;
        unset($timings['request']);
        $table = [];
        foreach ($timings as $key => $stats) {
            $table[$key] = round($stats['duration'] / 1000000, 2) . 'ms';
        }
        return new CollectorRendering(
            'Timing',
            round($this->timings['request']['duration'] / 1000000, 2) . 'ms',
            $table,
        );
    }
}
