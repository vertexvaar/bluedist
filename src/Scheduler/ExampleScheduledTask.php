<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Scheduler;

use VerteXVaaR\BlueScheduler\Attribute\ScheduledTask;
use VerteXVaaR\BlueScheduler\CliRequest;
use VerteXVaaR\BlueScheduler\Task\AbstractTask;

use function var_export;

#[ScheduledTask(identifier: 'First schedule of example task', interval: 60 * 60, config: ['hourly'])]
#[ScheduledTask(identifier: 'Second schedule of example task', interval: 86400, config: ['daily'])]
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
