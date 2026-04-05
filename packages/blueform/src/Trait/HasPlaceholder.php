<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

trait HasPlaceholder
{
    protected(set) ?string $placeholder = null;

    public function setPlaceholder(?string $placeholder = null): static
    {
        $this->placeholder = $placeholder;
        return $this;
    }
}
