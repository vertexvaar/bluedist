<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Decorator;

use Twig\Environment as View;
use VerteXVaaR\BlueDebug\Service\Stopwatch;

class TwigDecorator extends View
{
    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(
        private readonly View $view,
        private readonly Stopwatch $stopwatch,
    ) {
    }

    public function render($name, array $context = []): string
    {
        $this->stopwatch->start('render.' . $name);
        $return = $this->view->render($name, $context);
        $this->stopwatch->stop('render.' . $name);
        return $return;
    }
}
