<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Token;
use SvenHK\Maerquin\Exception\Account\PasswordResetException;
use SvenHK\Maerquin\Repository\TokenRepository;
use SvenHK\Maerquin\Repository\UserRepository;

class PasswordResetFormHandler
{
    public function __construct(
        private readonly TokenRepository $tokenRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function handle(
        Request $request,
        Token $token,
    ): void {
        $formResolver = FormResolver::createFromRequest($request);

        $password = $formResolver->getValue('password', 'password');
        $passwordRepeat = $formResolver->getValue('passwordRepeat', 'password');

        if (strlen($password) < 8) {
            throw PasswordResetException::tooShort();
        }

        if ($password !== $passwordRepeat) {
            throw PasswordResetException::notMatching();
        }

        $token->getUser()->updatePassword($password);
        $token->getUser()->verifyEmail();

        $this->userRepository->save($token->getUser());
        $this->tokenRepository->delete($token);
    }
}
