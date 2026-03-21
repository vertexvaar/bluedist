<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueValidation\Rule;

use VerteXVaaR\BlueValidation\ValidationError;

use function CoStack\Lib\array_filter_recursive;
use function ctype_digit;
use function feof;
use function fread;
use function fseek;
use function ftell;
use function gettype;
use function trim;

use const PHP_INT_MAX;

class Required implements Rule
{
    public function validate(mixed $value): ?ValidationError
    {
        return match (gettype($value)) {
            'string' => $this->validateString($value),
            'integer' => $this->validateInteger($value),
            'double' => $this->validateDouble($value),
            'array' => $this->validateArray($value),
            'object' => $this->validateObject($value),
            'resource' => $this->validateResource($value),
            'resource (closed)' => new ValidationError($this, $value, 'required.but.closed_resource'),
            'NULL' => new ValidationError($this, $value, 'required.but.null'),
            default => null,
        };
    }

    public function validateString(string $value): ?ValidationError
    {
        $trimmedValue = trim($value);
        if ('' === $trimmedValue) {
            return new ValidationError($this, $value, 'required.but.empty_string');
        }
        if (0 === (int)$trimmedValue && ctype_digit($trimmedValue)) {
            return new ValidationError($this, $value, 'required.but.zero');
        }
        return null;
    }

    public function validateInteger(int $value): ?ValidationError
    {
        if (0 === $value) {
            return new ValidationError($this, $value, 'required.but.zero');
        }
        return null;
    }

    public function validateDouble(float $value): ?ValidationError
    {
        if (0.0 === $value) {
            return new ValidationError($this, $value, 'required.but.zero');
        }
        return null;
    }

    public function validateArray(array $value): ?ValidationError
    {
        if ([] === array_filter_recursive($value, PHP_INT_MAX)) {
            return new ValidationError($this, $value, 'required.but.empty_array');
        }
        return null;
    }

    public function validateObject(object $value): ?ValidationError
    {
        if ([] === array_filter_recursive((array)$value, PHP_INT_MAX)) {
            return new ValidationError($this, $value, 'required.but.empty_object');
        }
        return null;
    }

    /** @param resource $value */
    public function validateResource($value): ?ValidationError
    {
        $current = ftell($value);
        try {
            fread($value, 1);
            $eof = feof($value);
        } finally {
            fseek($value, $current);
        }
        if ($eof) {
            return new ValidationError($this, $value, 'required.but.empty_resource');
        }
        return null;
    }
}
