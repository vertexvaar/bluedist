<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueValidation\Rule;

use VerteXVaaR\BlueValidation\ValidationError;
use VerteXVaaR\BlueValidation\ValidationRule;

use function is_numeric;
use function sprintf;

readonly class Min implements ValidationRule
{
    public const int CODE = 1775594513;

    public function __construct(
        private int|float $min,
    ) {}

    public function validate(bool $submitted, mixed $value): ?ValidationError
    {
        if (!$submitted || $value === null || $value === '') {
            return null;
        }
        if (!is_numeric($value) || (float)$value < $this->min) {
            return new ValidationError(
                self::CODE,
                ['{min}' => $this->min],
                sprintf('This value should be %s or more.', $this->min),
            );
        }
        return null;
    }
}
