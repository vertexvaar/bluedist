<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Element\Form\Button;

use VerteXVaaR\BlueForm\Element\Form\FormElement;

class Submit extends FormElement
{
    protected(set) bool $isFullWidth = false;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->condition = function (): bool {
            return $this->context === null || !$this->context->isShow();
        };
    }

    public function setIsFullWidth(bool $isFullWidth = true): static
    {
        $this->isFullWidth = $isFullWidth;
        return $this;
    }
}
