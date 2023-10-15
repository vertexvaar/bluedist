<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConsole;

use Symfony\Component\Console\Application;

class BlueApplication extends Application
{
    public function __construct(private readonly CommandRegistry $registry)
    {
        parent::__construct('BlueApplication', '1.0.0');
        foreach ($this->registry->commands as $command) {
            $this->add($command);
        }
    }
}
