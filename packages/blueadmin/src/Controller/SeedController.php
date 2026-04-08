<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueAdmin\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Throwable;
use VerteXVaaR\BlueAuth\Routing\Attributes\AuthorizedRoute;
use VerteXVaaR\BlueSeed\SeedService;
use VerteXVaaR\BlueWeb\Controller\AbstractController;
use VerteXVaaR\BlueWeb\Controller\Attribute\AsController;
use VerteXVaaR\BlueWeb\Enum\Severity;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

#[AsController]
class SeedController extends AbstractController
{
    private SeedService $seedService;

    #[Required]
    public function injectSeedService(SeedService $seedService): void
    {
        $this->seedService = $seedService;
    }

    #[AuthorizedRoute('/admin/seeds', method: Route::GET, requiredRoles: ['admin'])]
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute('session');
        $flashMessages = $this->flashMessageService->get($session);

        return $this->render('@vertexvaar_blueadmin/seeds/index.html.twig', [
            'seeders' => $this->seedService->seeders,
            'flashMessages' => $flashMessages,
        ]);
    }

    #[AuthorizedRoute('/admin/seeds/run/{name}', method: Route::POST, requiredRoles: ['admin'])]
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $name = $request->getAttribute('route')->matches['name'];
        $session = $request->getAttribute('session');

        try {
            $this->seedService->seed($name);
            $this->flashMessageService->add(
                $session,
                'Success',
                sprintf('Seeder "%s" executed successfully.', $name),
                Severity::SUCCESS,
            );
        } catch (Throwable $e) {
            $this->flashMessageService->add(
                $session,
                'Error',
                sprintf('Error executing seeder "%s": %s', $name, $e->getMessage()),
                Severity::ERROR,
            );
        }

        return $this->redirect('/admin/seeds');
    }
}
