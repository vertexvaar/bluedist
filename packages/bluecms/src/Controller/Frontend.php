<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueCms\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueCms\Entity\Page;
use VerteXVaaR\BlueWeb\Controller\AbstractController;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

use function base64_encode;

class Frontend extends AbstractController
{
    #[Route(path: '/cms{page:/.*?}', priority: -50)]
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $pageIdentifier = $request->getAttribute('route')->matches['page'];
        $page = $this->repository->findByIdentifier(Page::class, base64_encode($pageIdentifier));
        return $this->render('@vertexvaar_bluecms/frontend/show.html.twig', ['page' => $page]);
    }
}
