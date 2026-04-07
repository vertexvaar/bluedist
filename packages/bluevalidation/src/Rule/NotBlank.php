<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueValidation\Rule;

use VerteXVaaR\BlueValidation\ValidationError;
use VerteXVaaR\BlueValidation\ValidationRule;

readonly class NotBlank implements ValidationRule
{
    public const int CODE = 1775594546;

    public function validate(bool $submitted, mixed $value): ?ValidationError
    {
        if (!$submitted) {
            return null;
        }
        if ($value === null || $value === '') {
            return new ValidationError(self::CODE, [], 'This value should not be blank.');
        }
        return null;
    }
}
