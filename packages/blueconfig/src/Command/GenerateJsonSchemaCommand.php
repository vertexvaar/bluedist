<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VerteXVaaR\BlueConfig\Definition\DefinitionService;
use VerteXVaaR\BlueContainer\Generated\PackageExtras;

use function CoStack\Lib\concat_paths;
use function file_put_contents;
use function json_encode;

class GenerateJsonSchemaCommand extends Command
{
    public function __construct(
        private readonly DefinitionService $definitionService,
        private readonly PackageExtras $packageExtras,
    ) {
        parent::__construct('app:config:generate-json-schema');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $schema = $this->definitionService->getSchema();
        $jsonString = json_encode($schema, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        $configPath = $this->packageExtras->getPath($this->packageExtras->rootPackageName, 'config');
        file_put_contents(concat_paths($configPath, 'schema.json'), $jsonString);
        return Command::SUCCESS;
    }
}
