<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig\Structure;

readonly class OctalNumberNode implements Node
{
    public function __construct(
        private string $key,
        private string $name,
        private string $description,
        private string $default,
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDefault(): string
    {
        return $this->default;
    }

    public function getType(): string
    {
        return 'octal';
    }
}
