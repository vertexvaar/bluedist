<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueFoundation\Console;

use Symfony\Component\Console\Application;
use VerteXVaaR\BlueFoundation\Console\Command\CacheDotenvFileCommand;
use VerteXVaaR\BlueFoundation\Console\Command\CompileCommand;

class BlueFoundationApplication extends Application
{
    public function __construct(
        string $name = 'UNKNOWN',
        string $version = 'UNKNOWN',
    ) {
        parent::__construct($name, $version);
        $this->add(new CompileCommand());
        $this->add(new CacheDotenvFileCommand());
    }
}
