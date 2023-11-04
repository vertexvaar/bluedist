<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueScheduler;

use VerteXVaaR\BlueScheduler\Model\TaskExecution;
use VerteXVaaR\BlueScheduler\Task\AbstractTask;
use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;

use function strtr;
use function time;

readonly class Scheduler
{
    public function __construct(
        private SchedulerTaskRegistry $schedulerTaskRegistry,
        private Repository $repository,
    ) {
    }

    /**
     * Starts all the scheduled tasks. They are not executed in parallel.
     */
    public function run(CliRequest $cliRequest): void
    {
        $now = time();

        foreach ($this->schedulerTaskRegistry->tasks as $taskClass => $identifiers) {
            foreach ($identifiers as $identifier => $taskConfiguration) {
                $taskExecution = $this->getTaskExecution($taskClass . '->' . $identifier);
                if (
                    null === $taskExecution->lastExecution
                    || ($now - $taskExecution->lastExecution) >= $taskConfiguration['interval']
                ) {
                    /** @var AbstractTask $task */
                    $task = $taskConfiguration['service'];
                    $this->runTask($task, $identifier, $cliRequest, $taskConfiguration);
                    $taskExecution->lastExecution = $now;
                    $this->repository->persist($taskExecution);
                }
            }
        }
    }

    public function getTaskExecution(string $taskName): TaskExecution
    {
        $identifier = strtr($taskName, '\\', '_');
        $taskExecution = $this->repository->findByIdentifier(TaskExecution::class, $identifier);
        if (null === $taskExecution) {
            $taskExecution = new TaskExecution($identifier);
            $this->repository->persist($taskExecution);
        }
        return $taskExecution;
    }

    protected function runTask(
        AbstractTask $task,
        int|string $identifier,
        CliRequest $cliRequest,
        array $taskConfiguration,
    ): void {
        $task->run($identifier, $cliRequest, $taskConfiguration['config'] ?? []);
    }
}
