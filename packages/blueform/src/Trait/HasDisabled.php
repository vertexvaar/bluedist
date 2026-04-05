<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

trait HasDisabled
{
    protected bool $disabled = false;

    public function setDisabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }
}
