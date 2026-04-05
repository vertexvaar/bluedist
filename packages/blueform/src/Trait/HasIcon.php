<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

use VerteXVaaR\BlueForm\Element\Support\Icon;

trait HasIcon
{
    protected(set) ?Icon $icon = null;

    public function setIcon(Icon $icon): static
    {
        $this->icon = $icon;
        return $this;
    }
}
