<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueSprints\Mvc\Exception\IdentityAlreadySetException;

interface Entity
{
    public function getUuid(): ?string;

    /**
     * @throws IdentityAlreadySetException
     */
    public function setUuid(string $uuid): void;

    public function getIndexColumns(): array;
}
