<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Task;

use VerteXVaaR\BlueSprints\Task\AbstractTask;

class ExampleTask extends AbstractTask
{
    protected bool $verbose = false;

    public function run(): void
    {
        $this->verbose = $this->cliRequest->flagExists('--verbose');
        $this->verbose ? $this->printLine('Checking if arg "string" exists') : null;
        if ($this->cliRequest->hasArgument('string')) {
            $this->verbose ? $this->printLine('arg "string" exists, printing "string" value') : null;
            $this->printLine($this->cliRequest->getArgument('string'));
        } else {
            $this->verbose ? $this->printLine('arg "string" does not exist') : null;
        }
    }
}
