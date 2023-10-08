<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueSprints\Mvc\Exception\IdentityAlreadySetException;

abstract class AbstractModel implements Entity
{
    protected ?string $uuid = null;

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        if (!empty($this->uuid)) {
            throw new IdentityAlreadySetException($this);
        }
        $this->uuid = $uuid;
    }

    public function getIndexColumns(): array
    {
        return [];
    }
}
