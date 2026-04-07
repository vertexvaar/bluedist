<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueValidation\Rule;

use VerteXVaaR\BlueValidation\ValidationError;
use VerteXVaaR\BlueValidation\ValidationRule;

readonly class Required implements ValidationRule
{
    public const int CODE = 1775594567;

    public function validate(bool $submitted, mixed $value): ?ValidationError
    {
        if (!$submitted || $value === null || $value === '') {
            return new ValidationError(self::CODE, [], 'This value is required.');
        }
        return null;
    }
}
