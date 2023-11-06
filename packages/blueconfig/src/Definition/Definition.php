<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig\Definition;

use VerteXVaaR\BlueConfig\Structure\Node;

interface Definition
{
    public function get(): Node;
}
