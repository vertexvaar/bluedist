<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use VerteXVaaR\BlueWeb\Controller\AbstractController;
use VerteXVaaR\BlueWeb\Controller\Attribute\AsController;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

#[AsController]
class Welcome extends AbstractController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    #[Route(path: '/')]
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->debug('index action called');
        return $this->render('welcome/index.html.twig', [
            'session' => $request->getAttribute('session'),
            'strings' => ['foo', 'bar', 'baz'],
        ]);
    }
}
