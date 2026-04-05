<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

trait HasDefaultValue
{
    protected(set) mixed $defaultValue = null;

    public function setDefaultValue(mixed $defaultValue): static
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }
}
