<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

use VerteXVaaR\BlueForm\Element\Element;
use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

trait HasParent
{
    protected(set) ?Element $parent = null;

    public function setParent(Element $element): void
    {
        $this->parent = $element;
    }

    public function getEntity(): ?Entity
    {
        return $this->parent?->getEntity();
    }
}
