<?php
namespace VerteXVaaR\BlueSprints\Task;

/**
 * Class CliRequest
 *
 * @package VerteXVaaR\BlueSprints\Task
 */
class CliRequest
{
    /**
     * @return CliRequest
     */
    public static function createFromEnvironment()
    {
        return new self;
    }

    /**
     * @var array
     */
    protected $flags = [];

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @return CliRequest
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function __construct()
    {
        foreach ($_SERVER['argv'] as $argument) {
            $delimiterPosition = strpos($argument, '=');
            if ($delimiterPosition === false) {
                $this->flags[] = $argument;
            } else {
                $name = substr($argument, 0, $delimiterPosition);
                $value = substr($argument, ++$delimiterPosition);
                $this->arguments[$name] = $value;
            }
        }
        unset($_SERVER['argv']);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasArgument($name)
    {
        return isset($this->arguments[$name]);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getArgument($name)
    {
        return $this->arguments[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function flagExists($name)
    {
        return in_array($name, $this->flags);
    }
}
