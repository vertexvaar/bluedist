<?php

namespace VerteXVaaR\BlueWeb\Enum;

enum Severity: int
{
    case SUCCESS = 0;
    case INFO = 1;
    case WARNING = 2;
    case ERROR = 3;

    public function getBulmaClass(): string
    {
        return match ($this) {
            self::SUCCESS => 'is-success',
            self::INFO => 'is-info',
            self::WARNING => 'is-warning',
            self::ERROR => 'is-danger',
        };
    }
}
