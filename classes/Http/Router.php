<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Http;

use VerteXVaaR\BlueSprints\Utility\Files;

/**
 * Class Router
 */
class Router
{
    /**
     * @var string
     */
    const CONFIGURATION_FILENAME = 'configuration/routes.php';

    /**
     * @var array
     */
    protected static $routeStorage = [];

    /**
     * @var array[][]
     */
    protected $configuration = [];

    /**
     * @throws \Exception
     * @return Router
     */
    public function __construct()
    {
        if (Files::fileExists(self::CONFIGURATION_FILENAME)) {
            $this->configuration = Files::requireFile(self::CONFIGURATION_FILENAME);
        } else {
            throw new \Exception('The Router configuration does not exist', 1431886993);
        }
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function findMatchingRouteConfigurationForRequest(Request $request)
    {
        $path = $request->getPath();
        foreach ($this->configuration[$request->getMethod()] as $pattern => $possibleRoute) {
            if (preg_match('~^' . $pattern . '$~', $path)) {
                return $possibleRoute;
            }
        }
        throw new \Exception('Could not resolve a route for path "' . $path . '"', 1431887428);
    }

    /**
     * @param string $identifier
     * @return void
     */
    public static function collectRoutes($identifier)
    {
        list($vendor, $package) = explode('.', $identifier);
        $routes = Files::requireFile(
            implode(
                DIRECTORY_SEPARATOR,
                ['vendor', strtolower($vendor), strtolower($package), 'configuration', 'routes.php']
            )
        );
        foreach ($routes as $method => $route) {
            foreach ($route as $path => $configuration) {
                self::$routeStorage[$method][$path] = $configuration;
            }
        }
    }

    /**
     * @return array
     */
    public static function ejectRoutes()
    {
        $routes = self::$routeStorage;
        self::$routeStorage = [];
        return $routes;
    }
}
