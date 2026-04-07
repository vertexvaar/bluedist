<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Element\Container;

use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueForm\Element\Element;
use VerteXVaaR\BlueForm\FormContext;
use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

abstract class ContainerElement extends Element
{
    /** @var array<Element> */
    protected(set) array $children = [];
    protected(set) ?FormContext $context = null;

    /** @param array<Element> $children */
    public function setChildren(Element ...$children): static
    {
        $this->children = [];
        foreach ($children as $child) {
            $this->addChild($child);
        }
        return $this;
    }

    protected function addChild(Element $child): static
    {
        $child->setParent($this);
        if ($this->context !== null) {
            $child->setContext($this->context);
        }
        $this->children[] = $child;
        return $this;
    }

    public function setContext(FormContext $context): static
    {
        $this->context = $context;
        foreach ($this->children as $child) {
            $child->setContext($context);
        }
        return $this;
    }

    public function setEntity(Entity $entity): static
    {
        foreach ($this->children as $child) {
            $child->setEntity($entity);
        }
        return $this;
    }

    public function handleRequest(ServerRequestInterface $request): void
    {
        foreach ($this->children as $child) {
            $child->handleRequest($request);
        }
    }

    public function hasValidationErrors(): bool
    {
        foreach ($this->children as $child) {
            if ($child->hasValidationErrors()) {
                return true;
            }
        }
        return false;
    }

    protected function writeToEntity(Entity $entity): static
    {
        foreach ($this->children as $child) {
            $child->writeToEntity($entity);
        }

        return $this;
    }
}
