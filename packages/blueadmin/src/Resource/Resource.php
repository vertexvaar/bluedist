<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueAdmin\Resource;

use ReflectionClass;

use function str_ends_with;
use function str_replace;
use function strtolower;
use function substr;

abstract class Resource
{
    abstract public static function model(): string;

    abstract public static function formSchema(): array;

    abstract public static function tableColumns(): array;

    public static function slug(): string
    {
        $shortName = str_replace('\\', '', new ReflectionClass(static::class)->getShortName());
        if (str_ends_with($shortName, 'Resource')) {
            $shortName = substr($shortName, 0, -8);
        }
        return strtolower($shortName) . 's';
    }
}
