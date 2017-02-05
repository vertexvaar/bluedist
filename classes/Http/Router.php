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
     * @var array[][]
     */
    protected $configuration = [];

    /**
     * @throws \Exception
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
}
