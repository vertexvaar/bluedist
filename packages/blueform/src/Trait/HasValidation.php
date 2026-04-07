<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Trait;

use VerteXVaaR\BlueValidation\ValidationResult;
use VerteXVaaR\BlueValidation\ValidationRule;

trait HasValidation
{
    /** @var ValidationRule[] */
    private array $validationRules = [];
    protected(set) ?ValidationResult $validationResult = null;

    public function addValidation(ValidationRule ...$rules): static
    {
        foreach ($rules as $rule) {
            $this->validationRules[] = $rule;
        }
        return $this;
    }

    public function setValidations(ValidationRule ...$rules): static
    {
        $this->validationRules = [];
        $this->addValidation(...$rules);
        return $this;
    }

    protected function runValidation(bool $submitted, mixed $value): void
    {
        $result = new ValidationResult();
        foreach ($this->validationRules as $rule) {
            $error = $rule->validate($submitted, $value);
            if ($error !== null) {
                $result->addError($error);
            }
        }
        $this->validationResult = $result;
    }

    public function getValidationErrors(): ValidationResult
    {
        return $this->validationResult ?? new ValidationResult();
    }

    public function hasValidationErrors(): bool
    {
        return !($this->validationResult?->isValid() ?? true);
    }
}
