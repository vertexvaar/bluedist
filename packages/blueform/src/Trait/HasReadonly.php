<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

trait HasReadonly
{
    protected bool $readonly = false;

    public function setReadonly(bool $readonly = true): static
    {
        $this->readonly = $readonly;
        return $this;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }
}
