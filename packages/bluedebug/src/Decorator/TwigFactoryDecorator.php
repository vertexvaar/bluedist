<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Decorator;

use Twig\Environment as View;
use VerteXVaaR\BlueDebug\Collector\Stopwatch;
use VerteXVaaR\BlueWeb\Template\TwigFactory;

readonly class TwigFactoryDecorator
{
    public function __construct(
        private TwigFactory $inner,
        private Stopwatch $stopwatch,
    ) {
    }

    public function create(): View
    {
        return new TwigDecorator($this->inner->create(), $this->stopwatch);
    }
}
