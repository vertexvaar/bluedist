<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Element;

use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueForm\Element\Container\ContainerElement;
use VerteXVaaR\BlueForm\FormContext;
use VerteXVaaR\BlueForm\Trait\HasSubmittedValue;
use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

class Form extends ContainerElement
{
    use HasSubmittedValue;

    protected(set) ?Entity $entity = null;

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function setEntity(Entity $entity): static
    {
        $this->entity = $entity;
        parent::setEntity($entity);
        return $this;
    }

    public function setContext(FormContext $context): static
    {
        $this->context = $context;
        parent::setContext($context);
        return $this;
    }

    public function handleRequest(ServerRequestInterface $request): void
    {
        if ($request->getMethod() === 'POST') {
            $this->setSubmittedValue($request->getParsedBody());
            parent::handleRequest($request);
        }
    }

    public function writeToEntity(Entity $entity): static
    {
        parent::writeToEntity($entity);
        return $this;
    }
}
