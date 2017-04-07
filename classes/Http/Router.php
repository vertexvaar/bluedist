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
    const CONFIGURATION_FILENAME = 'app/config/routes.php';

    /**
     * @var array[][]
     */
    protected $configuration = [
        Request::HTTP_METHOD_GET => [],
        Request::HTTP_METHOD_HEAD => [],
        Request::HTTP_METHOD_POST => [],
        Request::HTTP_METHOD_PUT => [],
        Request::HTTP_METHOD_DELETE => [],
        Request::HTTP_METHOD_CONNECT => [],
        Request::HTTP_METHOD_OPTIONS => [],
        Request::HTTP_METHOD_TRACE => [],
    ];

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        if (Files::fileExists(self::CONFIGURATION_FILENAME)) {
            $this->configuration = array_merge_recursive(
                $this->configuration,
                Files::requireFile(self::CONFIGURATION_FILENAME)
            );
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
