<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Task;

use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

class TaskExecution extends Entity
{
    public int $lastExecution;
}
