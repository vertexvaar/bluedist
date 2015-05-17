<?php
namespace VerteXVaaR\BlueSprints\Route;

use VerteXVaaR\BlueSprints\Http\Request;
use VerteXVaaR\BlueSprints\Utility\Files;

/**
 * Class Router
 *
 * @package VerteXVaaR\BlueSprints\Route
 */
class Router {

	/**
	 * @var string
	 */
	const configurationFileName = 'configuration/routes.php';

	/**
	 * @var array[][]
	 */
	protected $configuration = array();

	/**
	 * @throws \Exception
	 * @return Router
	 */
	public function __construct() {
		if (Files::fileExists(self::configurationFileName)) {
			$this->configuration = Files::requireFile(self::configurationFileName);
		} else {
			throw new \Exception('The Router configuration does not exist', 1431886993);
		}
	}

	/**
	 * @param Request $request
	 * @return array
	 * @throws \Exception
	 */
	public function findMatchingRouteForRequest(Request $request) {
		$requestUri = $request->getRequestUri();
		foreach ($this->configuration[$request->getMethod()] as $pattern => $possibleRoute) {
			if (preg_match('~' . $pattern . '~', $requestUri)) {
				return $possibleRoute;
			}
		}
		throw new \Exception('Could not resolve a route for "' . $requestUri . '"', 1431887428);
	}

}
