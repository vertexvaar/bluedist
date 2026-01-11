<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueContainer\Console;


use Symfony\Component\Console\Application;
use VerteXVaaR\BlueContainer\Console\Command\CompileCommand;

class BlueContainerApplication extends Application
{
    public function __construct(
        string $name = 'UNKNOWN',
        string $version = 'UNKNOWN',
    ) {
        parent::__construct($name, $version);
        $this->add(new CompileCommand());
    }
}
