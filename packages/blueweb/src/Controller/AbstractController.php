<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Controller;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Twig\Environment as View;
use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;
use VerteXVaaR\BlueWeb\FlashMessage\FlashMessageService;

abstract class AbstractController implements Controller
{
    /**
     * @param Repository $repository
     * @param View $view
     * @param FlashMessageService $flashMessageService
     * @param CacheInterface $cache
     */
    final public function __construct(
        protected readonly Repository $repository,
        protected readonly View $view,
        protected readonly FlashMessageService $flashMessageService,
        protected readonly CacheInterface $cache,
    ) {}

    protected function render(string $template, array $context = []): ResponseInterface
    {
        return new Response(200, ['Content-Type' => 'text/html'], $this->view->render($template, $context));
    }

    protected function redirect($url, $code = 303): ResponseInterface
    {
        return new Response($code, ['Location' => $url]);
    }
}
