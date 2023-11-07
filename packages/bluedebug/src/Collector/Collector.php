<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector;

use VerteXVaaR\BlueDebug\Rendering\CollectorRendering;

interface Collector
{
    public function render(): CollectorRendering;
}
