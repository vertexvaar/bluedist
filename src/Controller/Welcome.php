<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueDist\Model\Fruit;
use VerteXVaaR\BlueSprints\Mvc\AbstractController;
use VerteXVaaR\BlueSprints\Utility\Strings;

class Welcome extends AbstractController
{
    /**
     * @noinspection PhpUnused
     */
    public function index(): ResponseInterface
    {
        return $this->render('index.html.twig', ['strings' => ['foo', 'bar', 'baz']]);
    }

    /**
     * @noinspection PhpUnused
     */
    public function listFruits(): ResponseInterface
    {
        return $this->render('fruits.html.twig', ['fruits' => $this->repository->findAll(Fruit::class)]);
    }

    /**
     * @noinspection PhpUnused
     */
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
            $fruit = new Fruit(Strings::generateUuid());
            $fruit->color = $fruitData['color'];
            $fruit->name = $fruitData['name'];
            $this->repository->persist($fruit);
        }
        return $this->redirect('listFruits');
    }

    /**
     * @noinspection PhpUnused
     */
    public function createFruit(ServerRequestInterface $request): ResponseInterface
    {
        $arguments = $request->getParsedBody();
        if (isset($arguments['name'], $arguments['color'])) {
            $fruit = new Fruit(Strings::generateUuid());
            $fruit->color = $arguments['color'];
            $fruit->name = $arguments['name'];
            $this->repository->persist($fruit);
        }
        return $this->redirect('listFruits');
    }

    /**
     * @noinspection PhpUnused
     */
    public function editFruit(ServerRequestInterface $request): ResponseInterface
    {
        $fruit = $this->repository->findByUuid(Fruit::class, $request->getQueryParams()['fruit']);
        return $this->render('edit.html.twig', ['fruit' => $fruit]);
    }

    /**
     * @noinspection PhpUnused
     */
    public function updateFruit(ServerRequestInterface $request): ResponseInterface
    {
        $arguments = $request->getParsedBody();
        if (isset($arguments['uuid'], $arguments['name'], $arguments['color'])) {
            $fruit = $this->repository->findByUuid(Fruit::class, $arguments['uuid']);
            if (null === $fruit) {
                return $this->redirect('listFruits');
            }
            $fruit->name = $arguments['name'];
            $fruit->color = $arguments['color'];
            $this->repository->persist($fruit);
        }
        return $this->redirect('listFruits');
    }

    /**
     * @noinspection PhpUnused
     */
    public function deleteFruit(ServerRequestInterface $request): ResponseInterface
    {
        $fruit = $this->repository->findByUuid(Fruit::class, $request->getParsedBody()['fruit']);
        if (null !== $fruit) {
            $this->repository->delete($fruit);
        }
        return $this->redirect('listFruits');
    }
}
