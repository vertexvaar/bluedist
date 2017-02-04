<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Http;

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
     * @return Request
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function createFromEnvironment()
    {
        define('VXVR_BS_REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
        return new static;
    }

    /**
     * Request constructor.
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function __construct()
    {
        $this->method = VXVR_BS_REQUEST_METHOD;
        $this->path = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->arguments = self::escapeRequestArguments($_REQUEST);
    }

    /**
     * @param array|string $argument
     * @return array|string
     */
    protected static function escapeRequestArguments($argument)
    {
        if (is_string($argument)) {
            $argument = htmlspecialchars($argument);
        } elseif (is_array($argument)) {
            foreach ($argument as $index => $arg) {
                $argument[$index] = self::escapeRequestArguments($arg);
            }
        }
        return $argument;
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
}
