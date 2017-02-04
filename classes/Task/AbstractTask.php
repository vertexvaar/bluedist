<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Task;

/**
 * Class AbstractTask
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
