<?php

namespace VerteXVaaR\BlueDist\Controller\Api;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueDist\Model\Fruit;
use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;
use VerteXVaaR\BlueWeb\Controller\Attribute\AsController;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

use function json_encode;

#[AsController]
readonly class FruitsController
{
    public function __construct(
        private Repository $repository,
    ) {}

    #[Route(path: '/api/fruits')]
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $page = $request->getQueryParams()['page'] ?? 1;
        $perPage = $request->getQueryParams()['per_page'] ?? 10;

        $pagination = $this->repository->paginate(Fruit::class, $page, $perPage);

        return new Response(200, ['Content-Type' => 'application/json'], json_encode($pagination));
    }
}
