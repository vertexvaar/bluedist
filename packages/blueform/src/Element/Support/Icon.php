<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Element\Support;

use CoStack\Lib\Enum\Direction;

class Icon
{
    public function __construct(
        public string $icon,
        public Direction $position,
    ) {}
}
