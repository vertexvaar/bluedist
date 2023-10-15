<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConsole;

use Symfony\Component\Console\Command\Command;

readonly class CommandRegistry
{
    /**
     * @param array<Command> $commands
     */
    public function __construct(public array $commands)
    {
    }
}
