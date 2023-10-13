<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Model\SubFolder;

use VerteXVaaR\BlueSprints\Mvc\Entity;

class Leaf extends Entity
{
    public function __construct(string $uuid, public readonly int $number)
    {
        parent::__construct($uuid);
    }
}
