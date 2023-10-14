<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Task;

use VerteXVaaR\BlueSprints\Mvc\Entity;

class TaskExecution extends Entity
{
    public int $lastExecution;
}
