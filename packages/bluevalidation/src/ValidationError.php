<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueValidation;

use VerteXVaaR\BlueValidation\Rule\Rule;

readonly class ValidationError
{
    public function __construct(
        public Rule $rule,
        public mixed $value,
        public string $label,
    ) {}
}
