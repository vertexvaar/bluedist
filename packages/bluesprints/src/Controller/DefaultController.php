<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Controller;

use Psr\Http\Message\ResponseInterface;
use VerteXVaaR\BlueSprints\Mvcr\Controller\AbstractController;
use VerteXVaaR\BlueSprints\Routing\Attributes\Route;

class DefaultController extends AbstractController
{
    #[Route(path: '/.*', priority: -100)]
    public function index(): ResponseInterface
    {
        return $this->render('@vertexvaar_bluesprints/index.html.twig');
    }
}
