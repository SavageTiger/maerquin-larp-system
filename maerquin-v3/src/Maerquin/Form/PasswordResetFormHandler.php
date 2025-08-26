<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Token;
use SvenHK\Maerquin\Entity\User;
use SvenHK\Maerquin\Exception\MaerquinPasswordResetException;
use SvenHK\Maerquin\Repository\TokenRepository;
use SvenHK\Maerquin\Repository\UserRepository;

class PasswordResetFormHandler
{
    /**
     * @var TokenRepository
     */
    private readonly EntityRepository $tokenRepository;

    /**
     * @var UserRepository
     */
    private readonly EntityRepository $userRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->tokenRepository = $entityManager->getRepository(Token::class);
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    public function handle(
        Request $request,
        Token $token,
    ): void {
        $formResolver = FormResolver::createFromRequest($request);

        $password = $formResolver->getValue('password', 'password');
        $passwordRepeat = $formResolver->getValue('passwordRepeat', 'password');

        if (strlen($password) < 8) {
            throw MaerquinPasswordResetException::tooShort();
        }

        if ($password !== $passwordRepeat) {
            throw MaerquinPasswordResetException::notMatching();
        }

        $token->getUser()->updatePassword($password);

        $this->userRepository->save($token->getUser());
        $this->tokenRepository->delete($token);
    }
}
