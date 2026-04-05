<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

use Closure;

trait HasAccessors
{
    protected(set) null|Closure $getter = null;
    protected(set) null|Closure $setter = null;

    public function setGetter(Closure $getter): static
    {
        $this->getter = $getter;
        return $this;
    }

    public function setSetter(Closure $setter): static
    {
        $this->setter = $setter;
        return $this;
    }
}
