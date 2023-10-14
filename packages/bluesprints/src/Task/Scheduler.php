<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Task;

use VerteXVaaR\BlueSprints\Environment\Paths;
use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;

use function CoStack\Lib\concat_paths;
use function file_exists;
use function getenv;
use function strtr;
use function time;

use const PHP_INT_MAX;

readonly class Scheduler
{
    private array $tasks;

    public function __construct(
        private Paths $paths,
        private Repository $repository,
    ) {
        $tasksFile = concat_paths(getenv('VXVR_BS_ROOT'), $this->paths->config, 'tasks.php');
        $tasks = [];
        if (file_exists($tasksFile)) {
            $tasks = require $tasksFile;
        }
        $this->tasks = $tasks;
    }

    /**
     * Starts all the scheduled tasks. They are not executed in parallel.
     */
    public function run(CliRequest $cliRequest): void
    {
        $now = time();

        foreach ($this->tasks as $taskName => $taskConfiguration) {
            $taskExecution = $this->getTaskExecution($taskName);
            if (($now - $taskExecution->lastExecution) >= $taskConfiguration['interval']) {
                /** @var AbstractTask $task */
                $task = new $taskConfiguration['task']($cliRequest);
                if (!empty($taskConfiguration['arguments'])) {
                    $task->run(...$taskConfiguration['arguments']);
                } else {
                    $task->run();
                }
                $taskExecution->lastExecution = $now;
                $this->repository->persist($taskExecution);
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
