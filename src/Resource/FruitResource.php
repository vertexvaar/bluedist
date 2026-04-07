<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Resource;

use VerteXVaaR\BlueAdmin\Resource\Resource;
use VerteXVaaR\BlueDist\Model\Fruit;

class FruitResource extends Resource
{
    public static function model(): string
    {
        return Fruit::class;
    }

    public static function formSchema(): array
    {
        return ['name', 'color'];
    }

    public static function tableColumns(): array
    {
        return ['name', 'color'];
    }
}
