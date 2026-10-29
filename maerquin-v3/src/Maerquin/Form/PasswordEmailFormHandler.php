<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Slim\Psr7\Request;
use SvenHK\Maerquin\Exception\Request\MissingFormFieldException;
use SvenHK\Maerquin\Model\User as UserModel;
use SvenHK\Maerquin\Repository\PlayerRepository;
use SvenHK\Maerquin\Repository\UserRepository;

readonly class PasswordEmailFormHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PlayerRepository $playerRepository,
    ) {
    }

    /**
     * @throws MissingFormFieldException
     */
    public function handle(Request $request): null | UserModel
    {
        $formResolver = FormResolver::createFromRequest($request);

        $email = mb_strtolower(trim($formResolver->getValue('email', 'passwordEmail')));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return null;
        }

        $user = $this->userRepository->findByUsername($email);

        if ($user !== null) {
            return $user;
        }

        $player = $this->playerRepository->findByEmail($email);

        if ($player === null) {
            return null;
        }

        $user = $this->userRepository->findByPlayer($player->getId());

        if ($user === null) {
            $user = UserModel::create($player);
            $user->changeUsername($email);
            $this->userRepository->save($user);
        }

        return $user;
    }
}
