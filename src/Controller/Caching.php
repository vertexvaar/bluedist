<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;
use Twig\Environment as View;
use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;
use VerteXVaaR\BlueWeb\Controller\AbstractController;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

class Caching extends AbstractController
{
    public function __construct(Repository $repository, View $view, private readonly CacheInterface $cache)
    {
        parent::__construct($repository, $view);
    }

    #[Route(path: '/cache/index')]
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $value = $this->cache->get('app/cache_demo');
        return $this->render('cache/index.html.twig', ['cache_value' => $value]);
    }

    #[Route(path: '/cache/store', method: Route::POST)]
    public function store(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        if (isset($body['value'], $body['ttl'])) {
            $value = $body['value'];
            $ttl = (int)$body['ttl'];

            $this->cache->set('app/cache_demo', $value, $ttl);
        }
        return $this->redirect('/cache/index');
    }
}
