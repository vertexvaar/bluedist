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
     */
    public function __construct(CliRequest $cliRequest)
    {
        $this->cliRequest = $cliRequest;
    }

    /**
     * Does not return anything. Write with ->printLine() instead.
     */
    abstract public function run();

    /**
     * @param string $line
     * @return void
     */
    final protected function printLine(string $line)
    {
        echo $line . PHP_EOL;
    }
}
