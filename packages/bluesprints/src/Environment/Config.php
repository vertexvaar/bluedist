<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Environment;

use ReflectionClass;

readonly class Config
{
    public function __construct(
        public int $filePermissions = 0664,
        public int $folderPermissions = 0775,
        public string $cookieAuthName = 'bluesprints_auth',
    ) {
    }

    /**
     * @param array{
     *     filePermissions: int,
     *     folderPermissions: int,
     *     cookieAuthName: ''
     * } $options
     */
    public static function setOptions(array $options): array
    {
        $defaultConfig = new Config();
        $properties = [];
        $reflection = new ReflectionClass(self::class);
        foreach ($reflection->getProperties() as $property) {
            $name = $property->getName();
            $properties[$name] = $options[$name] ?? $property->getValue($defaultConfig);
        }
        return $properties;
    }
}
