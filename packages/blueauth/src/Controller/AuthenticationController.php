<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueAuth\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Contracts\Service\Attribute\Required;
use VerteXVaaR\BlueAuth\Dto\Login;
use VerteXVaaR\BlueAuth\Form\LoginForm;
use VerteXVaaR\BlueAuth\Service\AuthenticationService;
use VerteXVaaR\BlueWeb\Controller\AbstractController;
use VerteXVaaR\BlueWeb\Controller\Attribute\AsController;
use VerteXVaaR\BlueWeb\Enum\Severity;
use VerteXVaaR\BlueWeb\Routing\Attributes\Route;

use function sprintf;

#[AsController]
class AuthenticationController extends AbstractController
{
    private AuthenticationService $authenticationService;

    #[Required]
    public function injectAuthenticationService(AuthenticationService $authenticationService): void
    {
        $this->authenticationService = $authenticationService;
    }

    #[Route(path: '/login')]
    #[Route(path: '/login', method: Route::POST)]
    public function login(ServerRequestInterface $request): ResponseInterface
    {
        $this->authenticationService->forcePersistentSession($request);

        $session = $request->getAttribute('session');
        if ($session->isAuthenticated()) {
            return $this->redirect('/');
        }

        $form = new LoginForm();
        $form->handleRequest($request);

        if ($form->submitted) {
            if ($form->isValid()) {
                $login = new Login(Uuid::uuid4()->toString());
                $form->writeToEntity($login);
                $this->authenticationService->authorize($session, $login->username, $login->password);

                if ($session->isAuthenticated()) {
                    $this->flashMessageService->add(
                        $session,
                        'Login successful',
                        sprintf('You are now logged in as %s.', $login->username),
                        Severity::ERROR,
                    );
                    return $this->redirect('/');
                }
            }
            $this->flashMessageService->add(
                $session,
                'Login failed',
                'Check your username and/or password.',
                Severity::ERROR,
            );
        }

        $flashMessages = $this->flashMessageService->get($session);
        return $this->render('@vertexvaar_blueauth/login.html.twig', [
            'form' => $form,
            'flashMessages' => $flashMessages,
        ]);
    }

    #[Route(path: '/logout')]
    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute('session');
        $this->authenticationService->logout($session);
        return $this->redirect('/');
    }
}
