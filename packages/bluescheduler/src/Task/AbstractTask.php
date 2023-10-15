<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueScheduler\Task;

use VerteXVaaR\BlueScheduler\CliRequest;

abstract class AbstractTask implements Task
{
    /**
     * Does not return anything. Write with ->printLine() instead.
     */
    abstract public function run(string $identifier, CliRequest $cliRequest, array $config): void;

    final protected function printLine(string $line): void
    {
        echo $line . PHP_EOL;
    }
}
