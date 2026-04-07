<?php

namespace VerteXVaaR\BlueAdmin\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueAuth\Mvcr\Model\User;
use VerteXVaaR\BlueAuth\Routing\Attributes\AuthorizedRoute;
use VerteXVaaR\BlueWeb\Controller\AbstractController;
use VerteXVaaR\BlueWeb\Controller\Attribute\AsController;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

#[AsController]
class DashboardController extends AbstractController
{
    #[AuthorizedRoute('/admin', Route::GET, requiredRoles: ['admin'])]
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute('session');
        $username = $session->getUsername();
        $user = $this->repository->findByIdentifier(User::class, $username);

        return $this->render('@vertexvaar_blueadmin/dashboard/index.html.twig', ['user' => $user]);
    }
}
