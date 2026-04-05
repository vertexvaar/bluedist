<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

use function array_key_exists;
use function array_merge_recursive;
use function array_replace_recursive;
use function CoStack\Lib\array_assoc_flatten;
use function CoStack\Lib\evaluate;
use function is_array;

trait HasTagAttributes
{
    protected array $tagAttributes = [];

    public function setAttributes(array $attributes): static
    {
        $this->tagAttributes = array_replace_recursive($this->tagAttributes, $attributes);
        return $this;
    }

    public function addAttributes(array $attributes): static
    {
        $this->tagAttributes = array_merge_recursive($this->tagAttributes, $attributes);
        return $this;
    }

    public function setAttribute(string $name, mixed $value): static
    {
        $this->tagAttributes[$name] = $value;
        return $this;
    }

    public function getTagAttributes(): array
    {
        $value = evaluate($this->tagAttributes, $this);
        return array_assoc_flatten($value);
    }

    public function getTagAttribute(string $name): string|array|null
    {
        if (!array_key_exists($name, $this->tagAttributes)) {
            return null;
        }
        $value = $this->tagAttributes[$name];
        $value = evaluate($value, $this);
        if (is_array($value)) {
            $value = array_assoc_flatten($value);
        }
        return $value;
    }
}
