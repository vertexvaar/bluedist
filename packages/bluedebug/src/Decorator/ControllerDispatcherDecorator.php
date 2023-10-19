<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Decorator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueDebug\Service\Stopwatch;
use VerteXVaaR\BlueWeb\RequestHandler\ControllerDispatcher;

readonly class ControllerDispatcherDecorator implements RequestHandlerInterface
{
    public function __construct(
        private ControllerDispatcher $inner,
        private Stopwatch $stopwatch,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->stopwatch->start('controller');
        $return = $this->inner->handle($request);
        $this->stopwatch->stop('controller');
        return $return;
    }

}
