<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Service;

use Exception;

use function hrtime;

class Stopwatch
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

    public function getTimings(): array
    {
        return $this->timings;
    }
}
