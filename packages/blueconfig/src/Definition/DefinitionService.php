<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig\Definition;

use VerteXVaaR\BlueConfig\Structure\Node;
use VerteXVaaR\BlueConfig\Structure\ObjectNode;
use VerteXVaaR\BlueConfig\Structure\RootNode;

use function is_array;
use function octdec;

readonly class DefinitionService
{
    /**
     * @param array<Definition> $definitions
     */
    public function __construct(
        private iterable $definitions,
    ) {
    }

    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    public function getSchema(bool $useInternalSchema = false): array
    {
        $schemaDefinition = [];
        $schemaDefinition['$schema'] = 'https://json-schema.org/draft/2020-12/schema';
        $schemaDefinition['$id'] = 'bluesprints.config.schema.json';
        foreach ($this->definitions as $definition) {
            $node = $definition->get();
            if ($node instanceof RootNode) {
                $this->exportNode($node, $useInternalSchema, $schemaDefinition);
            } else {
                $schemaDefinition['properties'][$node->getKey()] ??= [];
                $this->exportNode($node, $useInternalSchema, $schemaDefinition['properties'][$node->getKey()]);
            }
        }
        return $schemaDefinition;
    }

    protected function exportNode(Node $node, bool $useInternalSchema, array &$schemaDefinition): void
    {
        $schemaDefinition['title'] = $node->getName();
        $schemaDefinition['description'] = $node->getDescription();
        $type = $node->getType();
        if (!$useInternalSchema) {
            $type = match ($type) {
                'octal' => 'integer',
                default => $type,
            };
        }
        $schemaDefinition['type'] = $type;
        $default = $node->getDefault();
        if (null !== $default) {
            $schemaDefinition['default'] = $default;
        }
        if ($node instanceof ObjectNode) {
            $schemaDefinition['properties'] ??= [];
            foreach ($node->getChildren() as $child) {
                $childKey = $child->getKey();
                $schemaDefinition['properties'][$childKey] = [];
                $this->exportNode($child, $useInternalSchema, $schemaDefinition['properties'][$childKey]);
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

    public function cast(array &$config, array $schemaDefinition = null): void
    {
        $schemaDefinition ??= $this->getSchema(true);
        foreach ($schemaDefinition['properties'] as $key => $subDefinition) {
            if (isset($config[$key])) {
                if ($subDefinition['type'] === 'object' && is_array($config[$key])) {
                    $this->cast($config[$key], $subDefinition);
                } else {
                    $config[$key] = match ($subDefinition['type']) {
                        'integer' => (int)$config[$key],
                        'string' => (string)$config[$key],
                        'number' => (float)$config[$key],
                        'octal' => $config[$key],
                    };
                }
            }
        }
    }
}
