<?php
namespace VerteXVaaR\BlueSprints\Task;

/**
 * Class AbstractTask
 *
 * @package VerteXVaaR\BlueSprints\Task
 */
abstract class AbstractTask
{
    /**
     * @var CliRequest
     */
    protected $cliRequest = null;

    /**
     * @param CliRequest $cliRequest
     * @return AbstractTask
     */
    public function __construct(CliRequest $cliRequest)
    {
        $this->cliRequest = $cliRequest;
    }

    /**
     * @return string
     */
    abstract public function run();

    /**
     * @param string $line
     * @return void
     */
    final protected function printLine($line)
    {
        echo $line . PHP_EOL;
    }
}
