<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc\Exception;

use Throwable;
use VerteXVaaR\BlueSprints\BluesprintsException;
use VerteXVaaR\BlueSprints\Mvc\Entity;

use function sprintf;

class IdentityAlreadySetException extends BluesprintsException
{
    public const CODE = 1696756209;
    private const MESSAGE = 'The identity of entity "%s":"%s" was already set.';

    public function __construct(public readonly Entity $entity, Throwable $previous = null)
    {
        parent::__construct(
            sprintf(self::MESSAGE, $this->entity::class, $this->entity->getUuid()),
            self::CODE,
            $previous
        );
    }
}
