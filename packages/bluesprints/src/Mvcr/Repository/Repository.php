<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvcr\Repository;

use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;
use VerteXVaaR\BlueSprints\Store\Store;

/**
 * @template T of Entity
 */
readonly class Repository
{
    /**
     * @param Store<T> $store
     */
    public function __construct(
        private Store $store,
    ) {}

    /**
     * @param class-string<T> $className
     * @param string $identifier
     *
     * @return null|T
     */
    public function findByIdentifier(string $className, string $identifier): ?Entity
    {
        return $this->store->findByIdentifier($className, $identifier);
    }

    /**
     * @param class-string<T> $className
     *
     * @return array<T>
     */
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
