<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Task;

use VerteXVaaR\BlueSprints\Utility\Files;
use VerteXVaaR\BlueSprints\Utility\Folders;

/**
 * Class Scheduler
 */
class Scheduler
{
    /**
     * @var CliRequest
     */
    protected $cliRequest = null;

    /**
     * @var array
     */
    protected $tasks = [];

    /**
     * @param CliRequest $cliRequest
     */
    public function __construct(CliRequest $cliRequest)
    {
        $this->cliRequest = $cliRequest;
        $this->tasks = Files::requireFile('config/tasks.php');
    }

    /**
     * Starts all the scheduled tasks. They are not executed in parallel.
     */
    public function run()
    {
        foreach ($this->tasks as $taskName => $taskConfiguration) {
            if ($this->isScheduled($taskName, $taskConfiguration)) {
                /** @var AbstractTask $task */
                $task = new $taskConfiguration['task']($this->cliRequest);
                if (!empty($taskConfiguration['arguments'])) {
                    echo call_user_func_array([$task, 'run'], $taskConfiguration['arguments']);
                } else {
                    echo $task->run();
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
        $taskFolder = Folders::createFolderForClassName(
            'database' . DIRECTORY_SEPARATOR . 'tasks',
            $taskClassName
        );
        $taskInformationFile = $taskFolder . DIRECTORY_SEPARATOR . $taskName;
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
    protected function update(string $taskName, array $taskConfiguration)
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
    protected function writeTaskInformation(string $taskName, string $taskClassName, array $taskInformation)
    {
        $taskInformationFile = $this->getTaskInformationFile($taskName, $taskClassName);
        Files::writeFileContents($taskInformationFile, serialize($taskInformation));
    }
}
