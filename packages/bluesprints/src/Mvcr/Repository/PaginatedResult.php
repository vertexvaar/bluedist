<?php

namespace VerteXVaaR\BlueSprints\Mvcr\Repository;

use JsonSerializable;
use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

/**
 * @template T of Entity
 */
readonly class PaginatedResult implements JsonSerializable
{
    /**
     * @param array<T> $results
     */
    public function __construct(
        private array $results,
        private int $total,
        private int $currentPage,
        private int $perPage,
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'results' => $this->results,
            'total' => $this->total,
            'currentPage' => $this->currentPage,
            'perPage' => $this->perPage,
        ];
    }
}
