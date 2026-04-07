<?php

namespace VerteXVaaR\BlueAdmin\Table;

use Closure;
use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

use function CoStack\Lib\evaluate;

readonly class Column
{
    public function __construct(
        protected string|Closure $title,
        protected string|Closure $property,
    ) {}

    public function getValue(?Entity $entity = null): string
    {
        return evaluate($this->property, $entity);
    }

    public function getTitle(?Entity $entity = null): string
    {
        return evaluate($this->title, $entity);
    }
}
