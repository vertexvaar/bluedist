<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

trait HasInitialValue
{
    protected(set) mixed $initialValue = null;

    public function setInitialValue(mixed $initialValue): static
    {
        $this->initialValue = $initialValue;
        return $this;
    }
}
