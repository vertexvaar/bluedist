<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

use VerteXVaaR\BlueForm\FormContext;

trait HasContext
{
    protected ?FormContext $context = null;

    public function setContext(FormContext $context): static
    {
        $this->context = $context;
        return $this;
    }
}
