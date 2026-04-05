<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

trait HasSubmittedValue
{
    protected(set) bool $submitted = false;
    protected(set) mixed $submittedValue = null;

    public function setSubmittedValue(mixed $submittedValue): static
    {
        $this->submitted = true;
        $this->submittedValue = $submittedValue;
        return $this;
    }
}
