<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueValidation\Rule;

use VerteXVaaR\BlueValidation\ValidationError;

interface Rule
{
    public function validate(mixed $value): ?ValidationError;
}
