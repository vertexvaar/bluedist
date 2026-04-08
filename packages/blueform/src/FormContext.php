<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm;

use VerteXVaaR\BlueForm\Enum\FormPurpose;

class FormContext
{
    public function __construct(
        public FormPurpose $purpose = FormPurpose::Edit,
    ) {}

    public function isShow(): bool
    {
        return $this->purpose === FormPurpose::Show;
    }

    public function isEdit(): bool
    {
        return $this->purpose === FormPurpose::Edit;
    }

    public function isDisabled(): bool
    {
        return $this->isShow();
    }

    public function isReadonly(): bool
    {
        return false;
    }
}
