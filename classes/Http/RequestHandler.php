<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Http;

use VerteXVaaR\BlueSprints\Mvc\AbstractController;

/**
 * Class RequestHandler
 */
class RequestHandler
{
    /**
     * @var Router
     */
    protected $router = null;

    /**
     * RequestHandler constructor.
     */
    public function __construct()
    {
        $this->router = new Router();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handleRequest(Request $request)
    {
        $routeConfiguration = $this->router->findMatchingRouteConfigurationForRequest($request);
        $response = new Response();
        /** @var AbstractController $controller */
        $controller = new $routeConfiguration['controller']($request, $response);
        $controller->callActionMethod($routeConfiguration);
        return $response;
    }
}
