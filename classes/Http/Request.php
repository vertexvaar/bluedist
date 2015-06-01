<?php
namespace VerteXVaaR\BlueSprints\Http;

/**
 * Class Request
 *
 * @package VerteXVaaR\BlueSprints\Http
 */
class Request implements RequestInterface
{

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
        $request = new self;
        $request->setMethod($_SERVER['REQUEST_METHOD']);
        $request->setPath(explode('?', $_SERVER['REQUEST_URI'])[0]);
        $request->setArguments(self::escapeRequestArguments($_REQUEST));
        return $request;
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
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        define('VXVR_BS_REQUEST_METHOD', $method);
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param string $key
     * @return string|array
     */
    public function getArgument($key = '')
    {
        return $this->arguments[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasArgument($key = '')
    {
        return array_key_exists($key, $this->arguments);
    }
}
