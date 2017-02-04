<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Task;

/**
 * Class ExampleTask
 */
class ExampleTask extends AbstractTask
{
    /**
     * @param string $optionalString
     * @return void
     */
    public function run(string $optionalString = '')
    {
        $this->printLine('Stuff' . $optionalString);
    }
}
