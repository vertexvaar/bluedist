<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

use Closure;

use function CoStack\Lib\evaluate;

trait HasCondition
{
    protected bool|Closure $condition = true;

    public function setCondition(bool|Closure $condition): static
    {
        $this->condition = $condition;
        return $this;
    }

    public function getCondition(): bool
    {
        return (bool) evaluate($this->condition, $this);
    }
}
