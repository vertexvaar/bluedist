<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Store;

use VerteXVaaR\BlueSprints\Mvc\Entity;

interface Store
{
    public function findByUuid(string $class, string $uuid): ?object;

    public function findAll(string $class): array;

    public function store(Entity $entity): void;

    public function delete(Entity $entity): void;
}
