<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueValidation\Rule;

use VerteXVaaR\BlueValidation\ValidationError;
use VerteXVaaR\BlueValidation\ValidationRule;

use function is_numeric;
use function sprintf;

readonly class Max implements ValidationRule
{
    public const int CODE = 1775594483;

    public function __construct(
        private int|float $max,
    ) {}

    public function validate(bool $submitted, mixed $value): ?ValidationError
    {
        if (!$submitted || $value === null || $value === '') {
            return null;
        }
        if (!is_numeric($value) || (float)$value > $this->max) {
            return new ValidationError(
                self::CODE,
                ['{max}' => $this->max],
                sprintf('This value should be %s or less.', $this->max),);
        }
        return null;
    }
}
