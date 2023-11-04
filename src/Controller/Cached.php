<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Controller;

use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use VerteXVaaR\BlueWeb\Caching\Attributes\ActionCache;
use VerteXVaaR\BlueWeb\Controller\AbstractController;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

use function sleep;

class Cached extends AbstractController
{
    #[Route('/cached')]
    #[ActionCache(5)]
    public function index(): ResponseInterface
    {
        sleep(1);
        return $this->render('cached/index.html.twig', ['renderTime' => new DateTimeImmutable('now')]);
    }
}
