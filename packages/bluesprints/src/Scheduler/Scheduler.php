<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Scheduler;

use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;
use VerteXVaaR\BlueSprints\Scheduler\Model\TaskExecution;
use VerteXVaaR\BlueSprints\Scheduler\Task\AbstractTask;

use function strtr;
use function time;

use const PHP_INT_MAX;

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
                if (($now - $taskExecution->lastExecution) >= $taskConfiguration['interval']) {
                    /** @var AbstractTask $task */
                    $task = $taskConfiguration['service'];
                    $task->run($identifier, $cliRequest, $taskConfiguration['config'] ?? []);
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
            $taskExecution->lastExecution = -PHP_INT_MAX;
            $this->repository->persist($taskExecution);
        }
        return $taskExecution;
    }
}
