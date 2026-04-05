<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

use function array_map;
use function array_shift;
use function implode;

trait HasElementAttributes
{
    protected(set) string $name = '';
    protected(set) string $label = '';

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getPathArray(): array
    {
        $pathArray = $this->parent?->getPathArray() ?? [];
        $pathArray[] = $this->name;
        return $pathArray;
    }

    public function getPathString(): string
    {
        return implode('.', $this->getPathArray());
    }

    public function getHtmlPathArray(): array
    {
        $pathArray = $this->getPathArray();
        $begin = array_shift($pathArray);
        $pathArray = array_map(static fn(string $path) => "[$path]", $pathArray);
        array_unshift($pathArray, $begin);
        return $pathArray;
    }

    public function getHtmlPathString(): string
    {
        return implode('', $this->getHtmlPathArray());
    }
}
