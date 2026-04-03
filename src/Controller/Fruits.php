<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Ramsey\Uuid\Uuid;
use VerteXVaaR\BlueAuth\Routing\Attributes\AuthorizedRoute;
use VerteXVaaR\BlueDist\Model\Fruit;
use VerteXVaaR\BlueWeb\Controller\AbstractController;
use VerteXVaaR\BlueWeb\Controller\Attribute\AsController;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

#[AsController]
class Fruits extends AbstractController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    #[Route(path: '/fruits')]
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $page = isset($queryParams['page']) && is_numeric($queryParams['page']) && $queryParams['page'] > 0
            ? (int)$queryParams['page']
            : 1;
        $pageSize = 10;
        $paginatedResult = $this->repository->paginate(Fruit::class, $page, $pageSize);
        return $this->render('fruits/list.html.twig', [
            'session' => $request->getAttribute('session'),
            'pagination' => $paginatedResult,
        ]);
    }

    #[Route(path: '/fruits/seed', method: Route::POST)]
    public function seed(): ResponseInterface
    {
        $fruitsData = [
            [
                'color' => 'red',
                'name' => 'Apple',
            ],
            [
                'color' => 'yellow',
                'name' => 'Banana',
            ],
            [
                'color' => 'black',
                'name' => 'Blackberry',
            ],
            [
                'color' => 'red',
                'name' => 'Strawberry',
            ],
        ];
        foreach ($fruitsData as $fruitData) {
            $fruit = new Fruit(Uuid::uuid4()->toString());
            $fruit->color = $fruitData['color'];
            $fruit->name = $fruitData['name'];
            $this->repository->persist($fruit);
        }
        return $this->redirect('/fruits');
    }

    #[Route(path: '/fruits/create', method: Route::POST)]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $arguments = $request->getParsedBody();
        if (isset($arguments['name'], $arguments['color'])) {
            $fruit = new Fruit(Uuid::uuid4()->toString());
            $fruit->color = $arguments['color'];
            $fruit->name = $arguments['name'];
            $this->repository->persist($fruit);
        }
        return $this->redirect('/fruits');
    }

    #[AuthorizedRoute(path: '/fruits/deleteall', method: Route::POST, requiredRoles: ['admin'])]
    public function deleteAll(ServerRequestInterface $request): ResponseInterface
    {
        $fruits = $this->repository->findAll(Fruit::class);
        foreach ($fruits as $fruit) {
            $this->repository->delete($fruit);
        }
        return $this->redirect('/fruits');
    }

    #[Route(path: '/fruits/{fruit}')]
    public function edit(ServerRequestInterface $request): ResponseInterface
    {
        $fruitIdentifier = $request->getAttribute('route')->matches['fruit'];
        $fruit = $this->repository->findByIdentifier(Fruit::class, $fruitIdentifier);
        return $this->render('fruits/edit.html.twig', [
            'session' => $request->getAttribute('session'),
            'fruit' => $fruit,
        ]);
    }

    #[AuthorizedRoute(path: '/fruits/{fruit}', method: Route::POST, requireAuthorization: true)]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() === 'GET') {
            return $this->redirect('/fruits');
        }
        $fruitIdentifier = $request->getAttribute('route')->matches['fruit'];
        $arguments = $request->getParsedBody();
        if (isset($arguments['name'], $arguments['color'])) {
            $fruit = $this->repository->findByIdentifier(Fruit::class, $fruitIdentifier);
            if (null === $fruit) {
                return $this->redirect('/fruits');
            }
            $fruit->name = $arguments['name'];
            $fruit->color = $arguments['color'];
            $this->repository->persist($fruit);
        }
        return $this->redirect('/fruits');
    }

    #[AuthorizedRoute(path: '/fruits/{fruit}/delete', method: Route::POST, requiredRoles: ['user'])]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $fruitIdentifier = $request->getAttribute('route')->matches['fruit'];
        $fruit = $this->repository->findByIdentifier(Fruit::class, $fruitIdentifier);
        if (null !== $fruit) {
            $this->repository->delete($fruit);
        }
        return $this->redirect('/fruits');
    }
}
