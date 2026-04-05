<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Element;

use VerteXVaaR\BlueForm\FormContext;
use VerteXVaaR\BlueForm\Trait\HasCondition;
use VerteXVaaR\BlueForm\Trait\HasElementAttributes;
use VerteXVaaR\BlueForm\Trait\HasIcon;
use VerteXVaaR\BlueForm\Trait\HasParent;
use VerteXVaaR\BlueForm\Trait\HasTagAttributes;
use VerteXVaaR\BlueForm\Trait\HasTemplateName;
use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

abstract class Element
{
    use HasCondition;
    use HasElementAttributes;
    use HasIcon;
    use HasParent;
    use HasTagAttributes;
    use HasTemplateName;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function setContext(FormContext $context): static
    {
        return $this;
    }

    abstract protected function writeToEntity(Entity $entity): static;
}
