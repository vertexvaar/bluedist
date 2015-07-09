<?php
namespace VerteXVaaR\BlueSprints\Task;

use VerteXVaaR\BlueSprints\Utility\Files;
use VerteXVaaR\BlueSprints\Utility\Folders;

/**
 * Class Scheduler
 *
 * @package VerteXVaaR\BlueSprints\Task
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
     * @return void
     */
    public function run()
    {
        foreach ($this->tasks as $taskName => $taskConfiguration) {
            if ($this->isScheduled($taskName, $taskConfiguration)) {
                /** @var AbstractTask $task */
                $task = new $taskConfiguration['task']($this->cliRequest);
                if (!empty($taskConfiguration['arguments'])) {
                    echo call_user_func_array(array($task, 'run'), $taskConfiguration['arguments']);
                } else {
                    echo $task->run();
                }
                $this->update($taskName, $taskConfiguration);
            }
        }
    }

    /**
     * @param CliRequest $cliRequest
     */
    public function __construct(CliRequest $cliRequest)
    {
        $this->cliRequest = $cliRequest;
        $this->tasks = $this->collectTasks();
    }

    /**
     * @param string $taskName
     * @param array $taskConfiguration
     * @return bool
     */
    protected function isScheduled($taskName, $taskConfiguration)
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
    protected function getTaskInformationFile($taskName, $taskClassName)
    {
        $taskFolder = Folders::createFolderForClassName(
            'database' . DIRECTORY_SEPARATOR . 'tasks',
            $taskClassName
        );
        $taskInformationFile = $taskFolder . DIRECTORY_SEPARATOR . $taskName;
        Files::touch($taskInformationFile, serialize([]));
        return $taskInformationFile;
    }

    /**
     * @param string $taskName
     * @param string $taskClassName
     * @return mixed
     */
    protected function getTaskInformation($taskName, $taskClassName)
    {
        return unserialize(Files::readFileContents($this->getTaskInformationFile($taskName, $taskClassName)));
    }

    /**
     * @param $taskName
     * @param $taskConfiguration
     * @return void
     */
    protected function update($taskName, $taskConfiguration)
    {
        $taskInformation = $this->getTaskInformation($taskName, $taskConfiguration['task']);
        $taskInformation['lastRun'] = time();
        $this->writeTaskInformation($taskName, $taskConfiguration['task'], $taskInformation);
    }

    /**
     * @param string $taskName
     * @param string $taskClassName
     * @param string $taskInformation
     * @return void
     */
    protected function writeTaskInformation($taskName, $taskClassName, $taskInformation)
    {
        $taskInformationFile = $this->getTaskInformationFile($taskName, $taskClassName);
        Files::writeFileContents($taskInformationFile, serialize($taskInformation));
    }

    /**
     * @return array
     */
    protected function collectTasks()
    {
        return Files::requireFile('configuration/tasks.php');
    }
}
