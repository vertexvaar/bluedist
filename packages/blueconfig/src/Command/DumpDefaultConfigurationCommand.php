<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Dumper;
use VerteXVaaR\BlueConfig\Config;
use VerteXVaaR\BlueConfig\Definition\DefinitionService;
use VerteXVaaR\BlueContainer\Generated\PackageExtras;

use function array_replace_recursive;
use function CoStack\Lib\concat_paths;
use function file_put_contents;

class DumpDefaultConfigurationCommand extends Command
{
    public function __construct(
        private readonly DefinitionService $definitionService,
        private readonly PackageExtras $packageExtras,
        private readonly Config $config,
    ) {
        parent::__construct('app:config:dump-default');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $defaultConfig = $this->definitionService->getDefaultConfig();

        $config = array_replace_recursive($defaultConfig, $this->config->get());

        $dumper = new Dumper(2);
        $yamlContent = $dumper->dump($config, 5);

        $configFile = concat_paths(
            $this->packageExtras->getPath($this->packageExtras->rootPackageName, 'config'),
            'config.yaml',
        );
        file_put_contents($configFile, $yamlContent);

        return Command::SUCCESS;
    }
}
