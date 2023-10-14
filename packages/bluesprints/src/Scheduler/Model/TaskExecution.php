<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Scheduler\Model;

use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

class TaskExecution extends Entity
{
    public int $lastExecution;
}
