<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Http\Server\RequestHandler;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueSprints\Environment\Environment;
use VerteXVaaR\BlueSprints\Mvc\AbstractController;
use VerteXVaaR\BlueSprints\Mvc\RedirectException;

class ControllerDispatcher implements RequestHandlerInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly Environment $environment
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('route');

        /** @var AbstractController $controller */
        $controller = $this->container->get($route['controller']);
        try {
            return $controller->{$route['action']}($request);
        } catch (RedirectException $exception) {
            $response = new Response();
            return $response
                ->withHeader('Location', $exception->getUrl())
                ->withStatus($exception->getStatus());
        }
    }
}
