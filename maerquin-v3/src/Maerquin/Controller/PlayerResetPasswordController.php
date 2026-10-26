<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use SvenHK\Maerquin\Form\PasswordResetFormHandler;
use SvenHK\Maerquin\Repository\TokenRepository;
use Symfony\Component\Process\Exception\LogicException;

class PlayerResetPasswordController extends Action
{
    public function __construct(
        private readonly TokenRepository $tokenRepository,
        private PasswordResetFormHandler $passwordResetFormHandler,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $token = $this->tokenRepository->findByPasswordResetHash(
            $this->request->getAttribute('hash'),
        );

        if ($token !== null && $token->isValid() === false) {
            $token = null;
        }

        if ($this->request->getMethod() === 'POST') {
            if ($token === null) {
                throw new LogicException('No valid token provided');
            }

            $this->passwordResetFormHandler->handle(
                $this->request,
                $token,
            );

            return new Response();
        }

        return $view->render(
            $this->response,
            'player_reset_password.html.twig',
            [
                'token' => $token,
                'persisted' => str_contains($this->request->getUri()->getPath(), '/persisted/'),
            ],
        );
    }
}
