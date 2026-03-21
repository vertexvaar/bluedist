<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Store;

use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

/**
 * @template T of Entity
 */
interface Store
{
    /** @return null|T */
    public function findByIdentifier(string $class, string $identifier): ?Entity;

    /** @param class-string<T> $class */
    public function findAll(string $class, ?int $limit = null, int $offset = 0): array;

    public function store(Entity $entity): void;

    public function delete(Entity $entity): void;

    /** @param class-string<T> $class */
    public function countAll(string $class): int;
}
