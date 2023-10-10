<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Task;

class CliRequest
{
    protected array $flags = [];

    protected array $arguments = [];

    public static function createFromEnvironment(): self
    {
        return new self();
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function __construct()
    {
        foreach ($_SERVER['argv'] ?? [] as $argument) {
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
