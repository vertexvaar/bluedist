<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Controller;

use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueAuth\Routing\Attributes\AuthorizedRoute;
use VerteXVaaR\BlueWeb\ActionCache\Attributes\ActionCache;
use VerteXVaaR\BlueWeb\Controller\AbstractController;
use VerteXVaaR\BlueWeb\Controller\Attribute\AsController;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

use function sleep;
use function version_compare;

#[AsController]
class Cached extends AbstractController
{
    #[Route('/cached')]
    #[ActionCache(5)]
    public function index(): ResponseInterface
    {
        sleep(1);
        return $this->render('cached/index.html.twig', ['renderTime' => new DateTimeImmutable('now')]);
    }

    #[Route('/cached/params')]
    #[Route('/cached/params/{foo}')]
    #[ActionCache(matches: ['foo'], params: ['foo'])]
    public function parametrized(ServerRequestInterface $request): ResponseInterface
    {
        $cacheControl = version_compare($request->getProtocolVersion(), '1.0', '==')
            ? $request->getHeaderLine('Pragma')
            : $request->getHeaderLine('Cache-Control');

        $foo = $request->getAttribute('route')->matches['foo']
            ?? $request->getQueryParams()['foo']
            ?? null;

        return $this->render('cached/parametrized.html.twig', [
            'session' => $request->getAttribute('session'),
            'renderTime' => new DateTimeImmutable('now'),
            'cacheControl' => $cacheControl,
            'foo' => $foo,
        ]);
    }

    #[AuthorizedRoute('/cached/session', requiredRoles: ['user'])]
    #[AuthorizedRoute('/cached/session/{foo}', requiredRoles: ['user'])]
    #[ActionCache(ttl: 15, matches: ['word'], sessionSpecific: true)]
    public function session(ServerRequestInterface $request): ResponseInterface
    {
        return $this->render('cached/session.html.twig', [
            'session' => $request->getAttribute('session'),
            'renderTime' => new DateTimeImmutable('now'),
        ]);
    }

    #[Route('/cached/clearCache')]
    public function clearCache(): ResponseInterface
    {
        $this->cache->clear();
        return $this->redirect('/');
    }
}
