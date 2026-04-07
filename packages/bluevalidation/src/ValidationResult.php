<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueValidation;

class ValidationResult
{
    /** @var ValidationError[] */
    protected(set) array $errors = [];

    public function addError(ValidationError $error): void
    {
        $this->errors[] = $error;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }
}
