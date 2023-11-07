<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueCms\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueAuth\Routing\Attributes\AuthorizedRoute;
use VerteXVaaR\BlueCms\Entity\Page;
use VerteXVaaR\BlueWeb\Controller\AbstractController;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

use function base64_encode;

class Backend extends AbstractController
{
    #[AuthorizedRoute(path: '/backend', requiredRoles: ['editor'])]
    public function index(): ResponseInterface
    {
        $pages = $this->repository->findAll(Page::class);
        return $this->render('@vertexvaar_bluecms/backend/index.html.twig', ['pages' => $pages]);
    }

    #[AuthorizedRoute(path: '/backend/page/new', requiredRoles: ['editor'])]
    public function pageNew(): ResponseInterface
    {
        return $this->render('@vertexvaar_bluecms/backend/page/new.html.twig');
    }

    #[AuthorizedRoute(path: '/backend/page/save', requiredRoles: ['editor'], method: Route::POST)]
    public function pageSave(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        $page = new Page(base64_encode($body['slug']));
        $page->title = $body['title'];
        $this->repository->persist($page);
        return $this->redirect('/backend');
    }
}
