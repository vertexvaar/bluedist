<?php

namespace VerteXVaaR\BlueForm\Trait;

use function CoStack\Lib\str_to_snake_case;
use function str_replace;
use function str_starts_with;
use function substr;

trait HasTemplateName
{
    public function getTemplateName(): string
    {
        $base = str_replace('\\', '/', static::class);
        if (str_starts_with($base, 'VerteXVaaR/BlueForm/Element/')) {
            $base = substr($base, strlen('VerteXVaaR/BlueForm/Element/'));
        }
        return str_to_snake_case($base);
    }
}
