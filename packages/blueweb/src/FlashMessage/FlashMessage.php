<?php

namespace VerteXVaaR\BlueWeb\FlashMessage;

use VerteXVaaR\BlueWeb\Enum\Severity;

readonly class FlashMessage
{
    public function __construct(
        public string $title,
        public string $message,
        public Severity $severity = Severity::INFO,
    ) {}
}
