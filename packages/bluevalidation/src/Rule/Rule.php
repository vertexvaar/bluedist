<?php

namespace VerteXVaaR\BlueValidation\Rule;

use VerteXVaaR\BlueValidation\ValidationError;

interface Rule
{
    public function validate(mixed $value): ?ValidationError;
}
