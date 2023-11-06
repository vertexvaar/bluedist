<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig\Structure;

readonly class RootNode extends ObjectNode
{
    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(
        private string $name,
        private string $description,
        private array $children,
    ) {
    }

    public function getKey(): string
    {
        return '';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return 'object';
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getDefault(): array
    {
        return [];
    }
}
