<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Token;
use SvenHK\Maerquin\Form\PasswordResetFormHandler;
use SvenHK\Maerquin\Repository\TokenRepository;
use Symfony\Component\Process\Exception\LogicException;

class PlayerResetPasswordController extends Action
{
    /**
     * @var TokenRepository
     */
    private EntityRepository $tokenRepository;

    public function __construct(
        EntityManager $entityManager,
        private PasswordResetFormHandler $passwordResetFormHandler,
    ) {
        $this->tokenRepository = $entityManager->getRepository(Token::class);
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
