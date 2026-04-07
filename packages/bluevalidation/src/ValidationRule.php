<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueValidation;

interface ValidationRule
{
    public function validate(bool $submitted, mixed $value): ?ValidationError;
}
