<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConsole;

use Symfony\Component\Console\Application;

class BlueApplication extends Application
{
    public function __construct(private readonly array $commands)
    {
        parent::__construct('BlueApplication', '1.0.0');
        foreach ($this->commands as $command) {
            $this->add($command);
        }
    }
}
