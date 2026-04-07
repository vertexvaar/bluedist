<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueValidation\Rule;

use VerteXVaaR\BlueValidation\ValidationError;
use VerteXVaaR\BlueValidation\ValidationRule;

use function mb_strlen;
use function sprintf;

readonly class MaxLength implements ValidationRule
{
    public const int CODE = 1775594496;

    public function __construct(
        private int $max,
    ) {}

    public function validate(bool $submitted, mixed $value): ?ValidationError
    {
        if (!$submitted || $value === null || $value === '') {
            return null;
        }
        if (mb_strlen((string)$value) > $this->max) {
            return new ValidationError(
                self::CODE,
                ['{max}' => $this->max],
                sprintf('This value is too long. It should have %d characters or less.', $this->max),
            );
        }
        return null;
    }
}
