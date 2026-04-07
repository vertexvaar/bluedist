<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueValidation;

readonly class ValidationError
{
    public function __construct(
        public int $code,
        public array $context,
        public string $message,
    ) {}
}
