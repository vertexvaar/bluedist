<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Scheduler;

use VerteXVaaR\BlueSprints\Scheduler\Attribute\ScheduledTask;
use VerteXVaaR\BlueSprints\Scheduler\CliRequest;
use VerteXVaaR\BlueSprints\Scheduler\Task\AbstractTask;

use function var_export;

#[ScheduledTask(identifier: 'First schedule of example task', interval: 10, config: ['first', 'second'])]
#[ScheduledTask(identifier: 'Second schedule of example task', interval: 5, config: ['foo', 'bar'])]
class ExampleScheduledTask extends AbstractTask
{
    public function run(string $identifier, CliRequest $cliRequest, array $config): void
    {
        $verbose = $cliRequest->flagExists('--verbose');
        $this->printLine(sprintf("Running %s with options: %s", $identifier, var_export($config, true)));
        $verbose ? $this->printLine('Checking if arg "string" exists') : null;
        if ($cliRequest->hasArgument('string')) {
            $verbose ? $this->printLine('arg "string" exists, printing "string" value') : null;
            $this->printLine($cliRequest->getArgument('string'));
        } else {
            $verbose ? $this->printLine('arg "string" does not exist') : null;
        }
    }
}
