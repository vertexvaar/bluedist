<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Task;

use VerteXVaaR\BlueSprints\Environment\Paths;
use VerteXVaaR\BlueSprints\Utility\Files;
use VerteXVaaR\BlueSprints\Utility\Folders;

use function CoStack\Lib\concat_paths;

class Scheduler
{
    protected array $tasks = [];

    public function __construct(private readonly Paths $paths)
    {
        $this->tasks = Files::requireFile('config/tasks.php');
    }

    /**
     * Starts all the scheduled tasks. They are not executed in parallel.
     */
    public function run(CliRequest $cliRequest): void
    {
        foreach ($this->tasks as $taskName => $taskConfiguration) {
            if ($this->isScheduled($taskName, $taskConfiguration)) {
                /** @var AbstractTask $task */
                $task = new $taskConfiguration['task']($cliRequest);
                if (!empty($taskConfiguration['arguments'])) {
                    $task->run(...$taskConfiguration['arguments']);
                } else {
                    $task->run();
                }
                $this->update($taskName, $taskConfiguration);
            }
        }
    }

    /**
     * @param string $taskName
     * @param array $taskConfiguration
     * @return bool
     */
    protected function isScheduled(string $taskName, array $taskConfiguration): bool
    {
        $scheduled = false;
        $taskInformation = $this->getTaskInformation($taskName, $taskConfiguration['task']);
        if (empty($taskInformation)) {
            $scheduled = true;
        } else {
            if ((time() - $taskInformation['lastRun']) >= $taskConfiguration['interval']) {
                $scheduled = true;
            }
        }
        return $scheduled;
    }

    /**
     * @param string $taskName
     * @param string $taskClassName
     * @return string
     */
    protected function getTaskInformationFile(string $taskName, string $taskClassName): string
    {
        $taskFolder = Folders::createFolderForClassName($this->paths->database, $taskClassName);
        $taskInformationFile = concat_paths($taskFolder, $taskName);
        Files::touch($taskInformationFile, 'a:0:{}');
        return $taskInformationFile;
    }

    /**
     * @param string $taskName
     * @param string $taskClassName
     * @return mixed
     */
    protected function getTaskInformation(string $taskName, string $taskClassName)
    {
        return unserialize(Files::readFileContents($this->getTaskInformationFile($taskName, $taskClassName)));
    }

    /**
     * @param string $taskName
     * @param array $taskConfiguration
     */
    protected function update(string $taskName, array $taskConfiguration): void
    {
        $taskInformation = $this->getTaskInformation($taskName, $taskConfiguration['task']);
        $taskInformation['lastRun'] = time();
        $this->writeTaskInformation($taskName, $taskConfiguration['task'], $taskInformation);
    }

    /**
     * @param string $taskName
     * @param string $taskClassName
     * @param array $taskInformation
     */
    protected function writeTaskInformation(string $taskName, string $taskClassName, array $taskInformation): void
    {
        $taskInformationFile = $this->getTaskInformationFile($taskName, $taskClassName);
        Files::writeFileContents($taskInformationFile, serialize($taskInformation));
    }
}
