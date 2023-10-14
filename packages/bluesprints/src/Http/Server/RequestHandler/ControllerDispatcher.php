<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Http\Server\RequestHandler;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueSprints\Mvcr\Controller\AbstractController;

readonly class ControllerDispatcher implements RequestHandlerInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('route');

        /** @var AbstractController $controller */
        $controller = $this->container->get($route['controller']);
        return $controller->{$route['action']}($request);
    }
}
