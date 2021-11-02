<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Model\SubFolder;

use VerteXVaaR\BlueSprints\Mvc\AbstractModel;

class Leaf extends AbstractModel
{
    protected int $number;

    public function __construct(int $number)
    {
        $this->number = $number;
    }

    public function getNumber(): int
    {
        return $this->number;
    }
}
