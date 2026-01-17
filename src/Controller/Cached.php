<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Controller;

use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;
use Twig\Environment as View;
use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;
use VerteXVaaR\BlueWeb\ActionCache\Attributes\ActionCache;
use VerteXVaaR\BlueWeb\Controller\AbstractController;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

use function sleep;
use function version_compare;

class Cached extends AbstractController
{
    public function __construct(
        Repository $repository,
        View $view,
        private readonly CacheInterface $cache,
    ) {
        parent::__construct($repository, $view);
    }

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
    public function parametrized(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $cacheControl = version_compare($serverRequest->getProtocolVersion(), '1.0', '==')
            ? $serverRequest->getHeaderLine('Pragma')
            : $serverRequest->getHeaderLine('Cache-Control');
        $foo = $serverRequest->getAttribute('route')->matches['foo'] ?? $serverRequest->getQueryParams()['foo'] ?? null;
        return $this->render('cached/parametrized.html.twig', [
            'renderTime' => new DateTimeImmutable('now'),
            'cacheControl' => $cacheControl,
            'foo' => $foo,
        ]);
    }

    #[Route('/cached/clearCache')]
    public function clearCache(): ResponseInterface
    {
        $this->cache->clear();
        return $this->redirect('/');
    }
}
