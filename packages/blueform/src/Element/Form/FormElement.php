<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Element\Form;

use Closure;
use CoStack\Lib\Exceptions\ArrayKeyPathDoesNotExistException;
use CoStack\Lib\Exceptions\ArrayPathTerminatesEarlyException;
use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueForm\Element\Element;
use VerteXVaaR\BlueForm\Trait\HasAccessors;
use VerteXVaaR\BlueForm\Trait\HasDefaultValue;
use VerteXVaaR\BlueForm\Trait\HasDisabled;
use VerteXVaaR\BlueForm\Trait\HasInitialValue;
use VerteXVaaR\BlueForm\Trait\HasPlaceholder;
use VerteXVaaR\BlueForm\Trait\HasReadonly;
use VerteXVaaR\BlueForm\Trait\HasSubmittedValue;
use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

use function CoStack\Lib\array_value;

abstract class FormElement extends Element
{
    use HasAccessors;
    use HasDefaultValue;
    use HasDisabled;
    use HasInitialValue;
    use HasPlaceholder;
    use HasReadonly;
    use HasSubmittedValue;

    public function isDisabled(): bool
    {
        return $this->disabled || ($this->context?->isDisabled() ?? false);
    }

    public function isReadonly(): bool
    {
        return $this->readonly || ($this->context?->isReadonly() ?? false);
    }

    public function setEntity(Entity $entity): static
    {
        if ($this->getter instanceof Closure) {
            $this->setInitialValue($this->getter->call($entity));
            return $this;
        }

        if (property_exists($entity, $this->name)) {
            $this->setInitialValue($entity->{$this->name});
            return $this;
        }

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->submittedValue ?? $this->initialValue ?? $this->defaultValue;
    }

    protected function writeToEntity(Entity $entity): static
    {
        if (!$this->submitted) {
            return $this;
        }

        if ($this->setter instanceof Closure) {
            $this->setter->call($entity, $this->submittedValue);
            return $this;
        }

        if (property_exists($entity, $this->name)) {
            $entity->{$this->name} = $this->submittedValue;
            return $this;
        }

        return $this;
    }

    public function handleRequest(ServerRequestInterface $request): void
    {
        $parsedBody = $request->getParsedBody();
        try {
            $pathString = $this->getPathString();
            $value = array_value($parsedBody, $pathString);
            $this->setSubmittedValue($value);
        } catch (ArrayKeyPathDoesNotExistException|ArrayPathTerminatesEarlyException) {
            $value = null;
        }

        $this->runValidation($this->submitted, $value);
    }
}
