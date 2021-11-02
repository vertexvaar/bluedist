<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Task;

/**
 * Class CliRequest
 */
class CliRequest
{
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
     */
    public static function createFromEnvironment()
    {
        return new self;
    }

    /**
     * CliRequest constructor.
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
    public function hasArgument(string $name): bool
    {
        return isset($this->arguments[$name]);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getArgument(string $name)
    {
        return $this->arguments[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function flagExists(string $name): bool
    {
        return in_array($name, $this->flags);
    }
}
