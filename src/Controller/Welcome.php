<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Controller;

use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueDist\Model\Fruit;
use VerteXVaaR\BlueDist\Model\SubFolder\Branch;
use VerteXVaaR\BlueDist\Model\SubFolder\Leaf;
use VerteXVaaR\BlueDist\Model\SubFolder\Tree;
use VerteXVaaR\BlueSprints\Mvc\AbstractController;
use VerteXVaaR\BlueSprints\Utility\Strings;

class Welcome extends AbstractController
{
    protected function index(): void
    {
        $this->templateRenderer->setVariable(
            'strings',
            [
                'foo',
                'bar',
                'baz',
            ]
        );
    }

    protected function listFruits(): void
    {
        $this->templateRenderer->setVariable('fruits', $this->repository->findAll(Fruit::class));
    }

    protected function createDemoFruits(): void
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
        $this->redirect('listFruits');
    }

    protected function createFruit(ServerRequestInterface $request): void
    {
        $arguments = $request->getParsedBody();
        if (isset($arguments['name'], $arguments['color'])) {
            $fruit = new Fruit(Strings::generateUuid());
            $fruit->color = $arguments['color'];
            $fruit->name = $arguments['name'];
            $this->repository->persist($fruit);
        }
        $this->redirect('listFruits');
    }

    protected function editFruit(ServerRequestInterface $request): void
    {
        $fruit = $this->repository->findByUuid(Fruit::class, $request->getQueryParams()['fruit']);
        $this->templateRenderer->setVariable('fruit', $fruit);
    }

    protected function updateFruit(ServerRequestInterface $request): void
    {
        $arguments = $request->getParsedBody();
        if (isset($arguments['uuid'], $arguments['name'], $arguments['color'])) {
            $fruit = $this->repository->findByUuid(Fruit::class, $arguments['uuid']);
            $fruit->name = $arguments['name'];
            $fruit->color = $arguments['color'];
            $this->repository->persist($fruit);
        }
        $this->redirect('listFruits');
    }

    protected function createTree(ServerRequestInterface $request): void
    {
        $arguments = $request->getParsedBody();
        $tree = new Tree(Strings::generateUuid());
        $tree->setGenus($arguments['genus']);
        $this->repository->persist($tree);
        $this->templateRenderer->setVariable('tree', $tree);
        $this->templateRenderer->setVariable('branches', range(1, $arguments['numberOfBranches']));
    }

    protected function newTree(): void
    {
    }

    protected function growBranches(ServerRequestInterface $request): void
    {
        $arguments = $request->getParsedBody();
        $tree = $this->repository->findByUuid(Tree::class, $arguments['tree']);
        $branches = [];
        foreach ($arguments['branches'] as $data) {
            $branch = new Branch(Strings::generateUuid());
            $branch->setLength((int)$data['length']);
            $branches[] = $branch;
        }
        $tree->setBranches($branches);
        $this->repository->persist($tree);
        $this->redirect('applyLeaves?tree=' . $tree->uuid);
    }

    protected function applyLeaves(ServerRequestInterface $request): void
    {
        $arguments = $request->getQueryParams();
        $this->templateRenderer->setVariable(
            'tree',
            $this->repository->findByUuid(Tree::class, $arguments['tree'])
        );
    }

    protected function addLeaf(ServerRequestInterface $request): void
    {
        $arguments = $request->getParsedBody();
        $tree = $this->repository->findByUuid(Tree::class, $arguments['tree']);
        $branch = $tree->getBranches()[$arguments['branch']];
        $leaves = $branch->getLeaves();
        $leaves[] = new Leaf(Strings::generateUuid(), count($leaves) + 1);
        $branch->setLeaves($leaves);
        $this->repository->persist($tree);
        $this->redirect('applyLeaves?tree=' . $tree->uuid);
    }

    protected function deleteFruit(ServerRequestInterface $request): void
    {
        $fruit = $this->repository->findByUuid(Fruit::class, $request->getParsedBody()['fruit']);
        $this->repository->delete($fruit);
        $this->redirect('listFruits');
    }
}
