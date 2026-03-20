<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueAuth\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use SensitiveParameter;
use Throwable;
use VerteXVaaR\BlueAuth\Mvcr\Model\Session;
use VerteXVaaR\BlueAuth\Mvcr\Model\User;
use VerteXVaaR\BlueConfig\Config;
use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;

use function getenv;
use function in_array;
use function password_verify;
use function time;

readonly class AuthenticationService
{
    public function __construct(
        private Repository $repository,
        private Config $config,
    ) {}

    public function authorize(
        Session $session,
        string $username,
        #[SensitiveParameter] string $password,
    ): void {
        // If there is a session for another user, delete the session first.
        if (!in_array($session->getUsername(), [null, $username], true)) {
            $this->logout($session);
            $session = new Session(Uuid::uuid4()->toString());
        }
        $user = $this->repository->findByIdentifier(User::class, $username);
        if (null === $user) {
            if ($session->isAuthenticated()) {
                // Destroy session if user does not exist (anymore)
                $this->logout($session);
            }

            return;
        }
        if (null !== $user->hashedPassword && password_verify($password, $user->hashedPassword)) {
            $session->authenticate($username);
            $this->setSessionJwtCookie($session);
            $this->repository->persist($session);
        }
    }

    public function logout(Session $session): void
    {
        $session->unauthenticate();
        setcookie($this->config->get('auth.cookieAuthName'), '', -1, '/');
        $this->repository->delete($session);
    }

    public function loadSessionFromRequest(ServerRequestInterface $request): Session
    {
        $jwt = $request->getCookieParams()[$this->config->get('auth.cookieAuthName')] ?? null;
        if (null === $jwt) {
            return new Session(Uuid::uuid4()->toString());
        }

        try {
            $decoded = (array)JWT::decode($jwt, new Key(getenv('APP_SECRET'), 'HS256'));
        } catch (Throwable) {
            return new Session(Uuid::uuid4()->toString());
        }

        $sessionIdentifier = $decoded['sub'];

        $session = $this->repository->findByIdentifier(Session::class, $sessionIdentifier);

        return $session ?? new Session(Uuid::uuid4()->toString());
    }

    public function forcePersistentSession(ServerRequestInterface $request): Session
    {
        $session = $request->getAttribute('session') ?? $this->loadSessionFromRequest($request);
        $this->repository->persist($session);
        $this->setSessionJwtCookie($session);

        return $session;
    }

    /**
     * @param mixed $session
     *
     * @return void
     */
    protected function setSessionJwtCookie(mixed $session): void
    {
        $payload = [
            'sub' => $session->identifier,
            'iss' => $_SERVER['HTTP_HOST'],
            'aud' => $_SERVER['HTTP_HOST'],
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600,
        ];

        $jwt = JWT::encode($payload, getenv('APP_SECRET'), 'HS256', 'app_secret');

        setcookie($this->config->get('auth.cookieAuthName'), $jwt, time() + 3600, '/');
    }
}
