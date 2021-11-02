<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Task;

abstract class AbstractTask
{
    protected CliRequest $cliRequest;

    public function __construct(CliRequest $cliRequest)
    {
        $this->cliRequest = $cliRequest;
    }

    /**
     * Does not return anything. Write with ->printLine() instead.
     */
    abstract public function run(): void;

    final protected function printLine(string $line): void
    {
        echo $line . PHP_EOL;
    }
}
