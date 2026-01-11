<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector;

use VerteXVaaR\BlueDebug\Rendering\CollectorRendering;
use VerteXVaaR\BlueSprints\Environment\Environment;

readonly class EnvironmentCollector implements Collector
{
    public function __construct(
        private Environment $environment,
    ) {}

    public function render(): CollectorRendering
    {
        return new CollectorRendering('Context', $this->environment->context->name);
    }
}
