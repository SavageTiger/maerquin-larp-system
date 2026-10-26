<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\Frontend;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Token;
use SvenHK\Maerquin\Repository\TokenRepository;
use SvenHK\Maerquin\Repository\UserRepository;

class VerifyEmailController extends Action
{
    public function __construct(
        private readonly TokenRepository $tokenRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $token = $this->getValidToken();

        if ($token !== null) {
            $this->verifyEmail($token);
        }

        return $this->renderVerifyEmailView($token !== null);
    }

    private function getValidToken(): null | Token
    {
        $token = $this->tokenRepository->findByEmailVerificationHash(
            (string)$this->request->getAttribute('hash'),
        );

        if ($token === null || $token->isValid() === false) {
            return null;
        }

        return $token;
    }

    private function verifyEmail(Token $token): void
    {
        $token->getUser()->verifyEmail();

        $this->userRepository->save($token->getUser());
    }

    private function renderVerifyEmailView(bool $verified): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'Frontend/verify_email.html.twig',
            [
                'verified' => $verified,
            ],
        );
    }
}
