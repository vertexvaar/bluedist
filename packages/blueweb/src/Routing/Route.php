<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Routing;

readonly class Route
{
    public function __construct(
        public string $method,
        public string $path,
        public string $controller,
        public string $action,
    ) {
    }
}
