<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector;

use RuntimeException;
use VerteXVaaR\BlueDebug\Rendering\CollectorRendering;

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
            throw new RuntimeException(sprintf("Timer not started for %s", $name));
        }
        $this->timings[$name]['end'] = hrtime(true);
        $this->timings[$name]['duration'] = $this->timings[$name]['end'] - $this->timings[$name]['start'];
    }

    public function render(): CollectorRendering
    {
        $timings = $this->timings;
        $requestStart = $timings['request']['start'] ?? 0;
        $requestDuration = $timings['request']['duration'] ?? 0;
        $requestDurationMs = round($requestDuration / 1000000, 2);
        unset($timings['request']);

        $table = [
            '_meta' => [
                'total_duration_ms' => $requestDurationMs,
            ],
        ];
        foreach ($timings as $key => $stats) {
            $table[$key] = [
                'start_ms' => round(($stats['start'] - $requestStart) / 1000000, 2),
                'duration_ms' => round($stats['duration'] / 1000000, 2),
            ];
        }

        return new CollectorRendering(
            'Timing',
            $requestDurationMs . 'ms',
            $table,
        );
    }
}
