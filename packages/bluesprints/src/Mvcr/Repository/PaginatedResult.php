<?php

namespace VerteXVaaR\BlueSprints\Mvcr\Repository;

use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

/**
 * @template T of Entity
 */
readonly class PaginatedResult
{
    /**
     * @param array<T> $results
     */
    public function __construct(
        public array $results,
        public int $total,
        public int $currentPage,
        public int $perPage,
    ) {}

    public function getTotalPages(): int
    {
        return (int)ceil($this->total / $this->perPage);
    }
}
