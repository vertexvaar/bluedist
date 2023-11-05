<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector;

use VerteXVaaR\BlueAuth\Mvcr\Model\Session;
use VerteXVaaR\BlueAuth\Mvcr\Model\User;
use VerteXVaaR\BlueDebug\CollectorRendering;
use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;

use function implode;

class SessionCollector implements Collector
{
    protected ?Session $session = null;

    public function __construct(
        private Repository $repository,
    ) {
    }

    public function collect(Session $session): void
    {
        $this->session = $session;
    }

    public function render(): CollectorRendering
    {
        $username = $this->session->getUsername();
        $table = [];
        if (null !== $username) {
            $user = $this->repository->findByIdentifier(User::class, $username);
            if (null !== $user) {
                $table['roles'] = implode(', ', $user->roles);
            }
        }
        return new CollectorRendering(
            'Session',
            $username ?: '(anonymous)',
            $table,
        );
    }
}
