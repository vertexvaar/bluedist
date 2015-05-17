<?php
namespace VerteXVaaR\BlueSprints\Http;

use VerteXVaaR\BlueSprints\Controller\AbstractController;
use VerteXVaaR\BlueSprints\Route\Router;

/**
 * Class RequestHandler
 *
 * @package VerteXVaaR\BlueSprints\Http
 */
class RequestHandler {

	/**
	 * @var Router
	 */
	protected $router = NULL;

	/**
	 * @return RequestHandler
	 */
	public function __construct() {
		$this->router = new Router();
	}

	/**
	 * @param Request $request
	 * @return Response
	 */
	public function handleRequest(Request $request) {
		$routeConfiguration = $this->router->findMatchingRouteForRequest($request);
		$response = new Response();
		/** @var AbstractController $controller */
		$controller = new $routeConfiguration['controller']($request, $response);
		call_user_func([$controller, $routeConfiguration['action']]);
		return $response;
	}
}
