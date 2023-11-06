<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig;

use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Yaml\Parser;
use VerteXVaaR\BlueContainer\Generated\PackageExtras;

use function CoStack\Lib\concat_paths;
use function hash_file;

readonly class ConfigFactory
{
    public function __construct(
        private PackageExtras $packageExtras,
    ) {
    }

    public function build(): Config
    {
        $configFile = concat_paths(
            $this->packageExtras->getPath($this->packageExtras->rootPackageName, 'config'),
            'config.yaml',
        );
        $yaml = new Parser();
        $config = $yaml->parseFile($configFile) ?? [];
        return new Config($config);
    }
}
