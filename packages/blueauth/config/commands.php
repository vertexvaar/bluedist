<?php

declare(strict_types=1);

use VerteXVaaR\BlueAuth\Command\AddRoleToUserCommand;
use VerteXVaaR\BlueAuth\Command\ChangeUserPasswordCommand;
use VerteXVaaR\BlueAuth\Command\CreateUserCommand;

return [
    CreateUserCommand::class,
    ChangeUserPasswordCommand::class,
    AddRoleToUserCommand::class,
];
