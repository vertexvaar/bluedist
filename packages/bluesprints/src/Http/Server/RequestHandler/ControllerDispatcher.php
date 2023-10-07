<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Http\Server\RequestHandler;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use VerteXVaaR\BlueSprints\Mvc\AbstractController;
use VerteXVaaR\BlueSprints\Mvc\RedirectException;
use VerteXVaaR\BlueSprints\Utility\Context;

use function define;
use function ob_end_clean;
use function ob_get_contents;
use function ob_start;

class ControllerDispatcher implements RequestHandlerInterface
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        ob_start();
        define('VXVR_BS_REQUEST_METHOD', $request->getMethod());

        $route = $request->getAttribute('route');
        $response = new Response();

        /** @var AbstractController $controller */
        $controller = $this->container->get($route['controller']);
        $content = '';
        try {
            $content = $controller->callActionMethod($route, $request);
        } catch (RedirectException $exception) {
            $response = $response->withHeader('Location', $exception->getUrl())->withStatus($exception->getStatus());
        }
        if ((new Context())->getCurrentContext() === Context::CONTEXT_DEVELOPMENT) {
            $content = ob_get_contents() . $content;
        }

        $response->getBody()->write($content);
        ob_end_clean();
        return $response;
    }
}
