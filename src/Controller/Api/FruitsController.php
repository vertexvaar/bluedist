<?php

namespace VerteXVaaR\BlueDist\Controller\Api;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use VerteXVaaR\BlueDist\Model\Fruit;
use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;
use VerteXVaaR\BlueWeb\Controller\Controller;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

use function json_encode;

readonly class FruitsController implements Controller
{
    public function __construct(
        private Repository $repository,
    ) {}

    #[Route(path: '/api/fruits')]
    public function index(): ResponseInterface
    {
        $pagination = $this->repository->paginate(Fruit::class);

        return new Response(200, ['Content-Type' => 'application/json'], json_encode($pagination));
    }
}
