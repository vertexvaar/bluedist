<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig\Provider;

interface Provider
{
    public function get(): array;
}
