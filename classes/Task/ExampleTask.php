<?php
namespace VerteXVaaR\BlueSprints\Task;

/**
 * Class ExampleTask
 *
 * @package VerteXVaaR\BlueSprints\Task
 */
class ExampleTask extends AbstractTask
{
    /**
     * @param string $optionalString
     * @return void
     */
    public function run($optionalString = '')
    {
        $this->printLine('Stuff' . $optionalString);
    }

}
