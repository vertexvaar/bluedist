<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueValidation;

class ValidationErrors
{
    public function __construct(
        public array $errors = [],
    ) {}

    public function merge(?self $validationErrors = null): static
    {
        if (null !== $validationErrors) {
            foreach ($validationErrors->errors as $error) {
                $this->errors[] = $error;
            }
        }
        return $this;
    }
}
