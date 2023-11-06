<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig\Definition;

use VerteXVaaR\BlueConfig\Structure\Node;
use VerteXVaaR\BlueConfig\Structure\ObjectNode;
use VerteXVaaR\BlueConfig\Structure\RootNode;

readonly class DefinitionService
{
    /**
     * @param array<Definition> $definitions
     */
    public function __construct(
        private array $definitions,
    ) {
    }

    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    public function getSchema(): array
    {
        $schemaDefinition = [];
        $schemaDefinition['$schema'] = 'https://json-schema.org/draft/2020-12/schema';
        $schemaDefinition['$id'] = 'bluesprints.config.schema.json';
        foreach ($this->definitions as $definition) {
            $node = $definition->get();
            if ($node instanceof RootNode) {
                $this->exportNode($node, $schemaDefinition);
            } else {
                $schemaDefinition['properties'][$node->getKey()] ??= [];
                $this->exportNode($node, $schemaDefinition['properties'][$node->getKey()]);
            }
        }
        return $schemaDefinition;
    }

    protected function exportNode(Node $node, array &$schemaDefinition): void
    {
        $schemaDefinition['title'] = $node->getName();
        $schemaDefinition['description'] = $node->getDescription();
        $schemaDefinition['type'] = $node->getType();
        $default = $node->getDefault();
        if (null !== $default) {
            $schemaDefinition['default'] = $default;
        }
        if ($node instanceof ObjectNode) {
            $schemaDefinition['properties'] = [];
            foreach ($node->getChildren() as $child) {
                $childKey = $child->getKey();
                $schemaDefinition['properties'][$childKey] = [];
                $this->exportNode($child, $schemaDefinition['properties'][$childKey]);
            }
        }
    }

    public function getDefaultConfig(array $schemaDefinition = null, mixed &$config = []): array
    {
        $schemaDefinition ??= $this->getSchema()['properties'];
        foreach ($schemaDefinition as $key => $definition) {
            if ($definition['type'] === 'object') {
                $this->getDefaultConfig($definition['properties'], $config[$key]);
            } else {
                $config[$key] = $definition['default'];
            }
        }
        return $config;
    }
}
