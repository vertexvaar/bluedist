<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Controller;

use Psr\Http\Message\ResponseInterface;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

class DefaultController extends AbstractController
{
    #[Route(path: '/.*', priority: -100)]
    public function index(): ResponseInterface
    {
        return $this->render('@vertexvaar_blueweb/index.html.twig');
    }
}
