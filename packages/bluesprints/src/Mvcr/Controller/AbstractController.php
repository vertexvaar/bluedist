<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvcr\Controller;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;
use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;

abstract class AbstractController implements Controller
{
    public function __construct(
        protected readonly Repository $repository,
        protected readonly Environment $view
    ) {
    }

    protected function render(string $template, array $context = []): ResponseInterface
    {
        return new Response(200, [], $this->view->render($template, $context));
    }

    protected function redirect($url, $code = 303): ResponseInterface
    {
        return new Response($code, ['Location' => $url]);
    }
}
