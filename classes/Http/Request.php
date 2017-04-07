<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Http;

use VerteXVaaR\BlueSprints\Mvc\AbstractController;
use VerteXVaaR\BlueSprints\Utility\Context;

/**
 * Class Request
 */
class Request
{
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_HEAD = 'HEAD';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_CONNECT = 'CONNECT';
    const HTTP_METHOD_OPTIONS = 'OPTIONS';
    const HTTP_METHOD_TRACE = 'TRACE';

    /**
     * @var string
     */
    protected $method = '';

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var string[]
     */
    protected $arguments = [];

    /**
     * Request constructor.
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct()
    {
        ob_start();
        define('VXVR_BS_REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
        $this->method = VXVR_BS_REQUEST_METHOD;
        $this->path = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->arguments = $this->escapeRequestArguments($_REQUEST);
    }

    /**
     * @return Response
     */
    public function process(): Response
    {
        $router = new Router();
        $routeConfiguration = $router->findMatchingRouteConfigurationForRequest($this);
        $response = new Response();
        /** @var AbstractController $controller */
        $controller = new $routeConfiguration['controller']($this, $response);
        $content = $controller->callActionMethod($routeConfiguration);
        if ((new Context())->getCurrentContext() === Context::CONTEXT_DEVELOPMENT) {
            $content = ob_get_contents() . $content;
        }
        ob_end_clean();
        return $response->appendContent($content);
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param string $key
     * @return string|array
     */
    public function getArgument(string $key)
    {
        return $this->arguments[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasArgument(string $key): bool
    {
        return array_key_exists($key, $this->arguments);
    }

    /**
     * @param array|string $argument
     * @return array|string
     */
    protected function escapeRequestArguments($argument)
    {
        if (is_string($argument)) {
            $argument = htmlspecialchars($argument);
        } elseif (is_array($argument)) {
            foreach ($argument as $index => $arg) {
                $argument[$index] = $this->escapeRequestArguments($arg);
            }
        }
        return $argument;
    }
}
