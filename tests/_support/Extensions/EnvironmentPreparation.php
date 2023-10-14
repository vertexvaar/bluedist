<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDistTest\Extensions;

use Codeception\Event\TestEvent;
use Codeception\Events;
use Codeception\Extension;

use function CoStack\Lib\concat_paths;
use function escapeshellarg;
use function exec;
use function getenv;

class EnvironmentPreparation extends Extension
{
    public static array $events = [
        Events::TEST_BEFORE => 'beforeTest',
        Events::TEST_AFTER => 'afterTest',
    ];

    public function beforeTest(TestEvent $event)
    {
    }

    public function afterTest(TestEvent $event)
    {
        $root = getenv('VXVR_BS_ROOT');
        exec('rm -rf ' . escapeshellarg(concat_paths($root . '/var')));
    }
}
