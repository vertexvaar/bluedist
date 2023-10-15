<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Decorator;

use VerteXVaaR\BlueDebug\Service\QueryCollector;
use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;
use VerteXVaaR\BlueSprints\Store\Store;

use function func_get_args;

class StoreDecorator implements Store
{
    public function __construct(
        private readonly Store $inner,
        private readonly QueryCollector $queryCollector,
    ) {
    }

    public function findByIdentifier(string $class, string $identifier): ?object
    {
        return $this->queryCollector->execute(
            'findByIdentifier',
            ['class' => $class],
            $this->inner->findByIdentifier(...),
            func_get_args()
        );
    }

    public function findAll(string $class): array
    {
        return $this->queryCollector->execute(
            'findAll',
            ['class' => $class],
            $this->inner->findAll(...),
            func_get_args()
        );
    }

    public function store(Entity $entity): void
    {
        $this->queryCollector->execute(
            'store',
            ['class' => $entity::class],
            $this->inner->store(...),
            func_get_args()
        );
    }

    public function delete(Entity $entity): void
    {
        $this->queryCollector->execute(
            'delete',
            ['class' => $entity::class],
            $this->inner->delete(...),
            func_get_args()
        );
    }
}
