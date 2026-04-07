<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueValidation\Rule;

use VerteXVaaR\BlueValidation\ValidationError;
use VerteXVaaR\BlueValidation\ValidationRule;

use function mb_strlen;
use function sprintf;

readonly class MinLength implements ValidationRule
{
    public const int CODE = 1775594469;

    public function __construct(
        private int $min,
    ) {}

    public function validate(bool $submitted, mixed $value): ?ValidationError
    {
        if (!$submitted || $value === null || $value === '') {
            return null;
        }
        if (mb_strlen((string)$value) < $this->min) {
            return new ValidationError(
                self::CODE,
                ['{min}' => $this->min],
                sprintf('This value is too short. It should have %d characters or more.', $this->min),
            );
        }
        return null;
    }
}
