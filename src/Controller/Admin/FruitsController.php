<?php

namespace VerteXVaaR\BlueDist\Controller\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use VerteXVaaR\BlueAuth\Routing\Attributes\AuthorizedRoute;
use VerteXVaaR\BlueDist\Form\FruitForm;
use VerteXVaaR\BlueDist\Model\Fruit;
use VerteXVaaR\BlueDist\Table\FruitTable;
use VerteXVaaR\BlueForm\Enum\FormPurpose;
use VerteXVaaR\BlueForm\FormContext;
use VerteXVaaR\BlueWeb\Controller\AbstractController;
use VerteXVaaR\BlueWeb\Controller\Attribute\AsController;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

#[AsController]
class FruitsController extends AbstractController
{
    #[AuthorizedRoute(path: '/admin/fruits', requiredRoles: ['admin'])]
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $fruits = $this->repository->findAll(Fruit::class);

        $table = new FruitTable();

        return $this->render('admin/list.html.twig', ['table' => $table, 'items' => $fruits]);
    }

    #[AuthorizedRoute(path: '/admin/fruits/create', requiredRoles: ['admin'])]
    public function createForm(ServerRequestInterface $request): ResponseInterface
    {
        $form = new FruitForm();
        $form->setAttribute('action', '/admin/fruits/create');

        return $this->render('admin/edit.html.twig', ['form' => $form]);
    }

    #[AuthorizedRoute(path: '/admin/fruits/create', method: Route::POST, requiredRoles: ['admin'])]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $fruit = new Fruit(Uuid::uuid4()->toString());
        $form = new FruitForm();
        $form->handleRequest($request);

        if ($form->submitted && $form->isValid()) {
            $form->writeToEntity($fruit);
            $this->repository->persist($fruit);
            return $this->redirect('/admin/fruits/' . $fruit->identifier);
        }

        return $this->redirect('/admin/fruits/create');
    }

    #[AuthorizedRoute(path: '/admin/fruits/{fruit}/edit', requiredRoles: ['admin'])]
    public function editForm(ServerRequestInterface $request): ResponseInterface
    {
        $identifier = $request->getAttribute('route')->matches['fruit'];
        $fruit = $this->repository->findByIdentifier(Fruit::class, $identifier);

        $form = new FruitForm();
        $form->setEntity($fruit);
        $form->setAttribute('action', '/admin/fruits/' . $identifier);

        return $this->render('admin/edit.html.twig', ['form' => $form]);
    }

    #[AuthorizedRoute(path: '/admin/fruits/delete-multiple', method: Route::POST, requiredRoles: ['admin'])]
    public function deleteMultiple(ServerRequestInterface $request): ResponseInterface
    {
        $identifiers = $request->getParsedBody()['ids'];
        foreach ($identifiers as $identifier) {
            $fruit = $this->repository->findByIdentifier(Fruit::class, $identifier);
            $this->repository->delete($fruit);
        }
        return $this->redirect('/admin/fruits');
    }

    #[AuthorizedRoute(path: '/admin/fruits/{fruit}', method: Route::POST, requiredRoles: ['admin'])]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $identifier = $request->getAttribute('route')->matches['fruit'];
        $fruit = $this->repository->findByIdentifier(Fruit::class, $identifier);

        $form = new FruitForm();
        $form->handleRequest($request);

        if ($form->submitted && $form->isValid()) {
            $form->writeToEntity($fruit);
            $this->repository->persist($fruit);
            return $this->redirect('/admin/fruits/' . $fruit->identifier);
        }

        return $this->redirect('/admin/fruits/' . $identifier . '/edit');
    }

    #[AuthorizedRoute(path: '/admin/fruits/{fruit}/delete', method: Route::POST, requiredRoles: ['admin'])]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $identifier = $request->getAttribute('route')->matches['fruit'];
        $fruit = $this->repository->findByIdentifier(Fruit::class, $identifier);
        $this->repository->delete($fruit);
        return $this->redirect('/admin/fruits');
    }

    #[AuthorizedRoute(path: '/admin/fruits/{fruit}', requiredRoles: ['admin'])]
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $identifier = $request->getAttribute('route')->matches['fruit'];
        $fruit = $this->repository->findByIdentifier(Fruit::class, $identifier);

        $form = new FruitForm();
        $form->setEntity($fruit);
        $form->setContext(new FormContext(FormPurpose::Show));

        return $this->render('admin/show.html.twig', ['form' => $form]);
    }
}
