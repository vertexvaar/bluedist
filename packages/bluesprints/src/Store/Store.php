<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Store;

use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

interface Store
{
    public function findByIdentifier(string $class, string $identifier): ?Entity;

    /** @param class-string<Entity> $class */
    public function findAll(string $class): array;

    public function store(Entity $entity): void;

    public function delete(Entity $entity): void;
}
