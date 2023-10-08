<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Model;

use VerteXVaaR\BlueSprints\Mvc\AbstractModel;
use VerteXVaaR\BlueSprints\Mvc\Entity;
use VerteXVaaR\BlueSprints\Mvc\Exception\IdentityAlreadySetException;

class Fruit extends AbstractModel
{
    protected string $name = '';

    protected string $color = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getIndexColumns(): array
    {
        return ['name'];
    }
}
