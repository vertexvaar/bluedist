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
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

class Welcome extends AbstractController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    #[Route(path: '/')]
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->debug('index action called');
        return $this->render('fruits/index.html.twig', [
            'session' => $request->getAttribute('session'),
            'strings' => ['foo', 'bar', 'baz'],
        ]);
    }

    #[Route(path: '/listFruits')]
    public function listFruits(): ResponseInterface
    {
        return $this->render('fruits/list.html.twig', ['fruits' => $this->repository->findAll(Fruit::class)]);
    }

    #[Route(path: '/createDemoFruits', method: Route::POST)]
    public function createDemoFruits(): ResponseInterface
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
        return $this->redirect('listFruits');
    }

    #[Route(path: '/createFruit', method: Route::POST)]
    public function createFruit(ServerRequestInterface $request): ResponseInterface
    {
        $arguments = $request->getParsedBody();
        if (isset($arguments['name'], $arguments['color'])) {
            $fruit = new Fruit(Uuid::uuid4()->toString());
            $fruit->color = $arguments['color'];
            $fruit->name = $arguments['name'];
            $this->repository->persist($fruit);
        }
        return $this->redirect('listFruits');
    }

    #[Route(path: '/editFruit')]
    public function editFruit(ServerRequestInterface $request): ResponseInterface
    {
        $fruit = $this->repository->findByIdentifier(Fruit::class, $request->getQueryParams()['fruit']);
        return $this->render('fruits/edit.html.twig', ['fruit' => $fruit]);
    }

    /**
     * 'GET' route registration only to be able to redirect the user for demonstration purposes.
     */
    #[Route(path: '/updateFruit')]
    #[AuthorizedRoute(path: '/updateFruit', method: 'POST', requireAuthorization: true)]
    public function updateFruit(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() === 'GET') {
            return $this->redirect('listFruits');
        }
        $arguments = $request->getParsedBody();
        if (isset($arguments['id'], $arguments['name'], $arguments['color'])) {
            $fruit = $this->repository->findByIdentifier(Fruit::class, $arguments['id']);
            if (null === $fruit) {
                return $this->redirect('listFruits');
            }
            $fruit->name = $arguments['name'];
            $fruit->color = $arguments['color'];
            $this->repository->persist($fruit);
        }
        return $this->redirect('listFruits');
    }

    #[AuthorizedRoute(path: '/deleteFruit', method: 'POST', requiredRoles: ['user'])]
    public function deleteFruit(ServerRequestInterface $request): ResponseInterface
    {
        $fruit = $this->repository->findByIdentifier(Fruit::class, $request->getParsedBody()['fruit']);
        if (null !== $fruit) {
            $this->repository->delete($fruit);
        }
        return $this->redirect('listFruits');
    }

    #[AuthorizedRoute(path: '/deleteAllFruits', method: 'POST', requiredRoles: ['admin'])]
    public function deleteAllFruits(ServerRequestInterface $request): ResponseInterface
    {
        $fruits = $this->repository->findAll(Fruit::class);
        foreach ($fruits as $fruit) {
            $this->repository->delete($fruit);
        }
        return $this->redirect('listFruits');
    }
}
