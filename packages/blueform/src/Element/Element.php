<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Element;

use VerteXVaaR\BlueForm\Trait\HasCondition;
use VerteXVaaR\BlueForm\Trait\HasContext;
use VerteXVaaR\BlueForm\Trait\HasElementAttributes;
use VerteXVaaR\BlueForm\Trait\HasIcon;
use VerteXVaaR\BlueForm\Trait\HasParent;
use VerteXVaaR\BlueForm\Trait\HasTagAttributes;
use VerteXVaaR\BlueForm\Trait\HasTemplateName;
use VerteXVaaR\BlueForm\Trait\HasValidation;
use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

abstract class Element
{
    use HasCondition;
    use HasContext;
    use HasElementAttributes;
    use HasIcon;
    use HasParent;
    use HasTagAttributes;
    use HasTemplateName;
    use HasValidation;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    abstract protected function writeToEntity(Entity $entity): static;
}
