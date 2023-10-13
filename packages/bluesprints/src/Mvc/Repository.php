<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueSprints\Store\Store;

readonly class Repository
{
    public function __construct(private Store $store)
    {
    }

    public function findByUuid(string $className, string $uuid): ?Entity
    {
        return $this->store->findByUuid($className, $uuid);
    }

    public function findAll(string $className): array
    {
        return $this->store->findAll($className);
    }

    public function persist(Entity $entity): void
    {
        $this->store->store($entity);
    }

    public function delete(Entity $entity): void
    {
        $this->store->delete($entity);
    }
}
