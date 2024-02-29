<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig\Provider;

use Symfony\Component\Yaml\Parser;
use VerteXVaaR\BlueContainer\Generated\PackageExtras;

use function CoStack\Lib\concat_paths;
use function file_exists;

readonly class ConfigurationFileProvider implements Provider
{
    public function __construct(
        private PackageExtras $packageExtras,
    ) {
    }

    public function get(): array
    {
        $configFile = concat_paths(
            $this->packageExtras->getPath($this->packageExtras->rootPackageName, 'config'),
            'config.yaml',
        );
        if (!file_exists($configFile)) {
            return [];
        }
        $yaml = new Parser();
        return $yaml->parseFile($configFile) ?? [];
    }
}
