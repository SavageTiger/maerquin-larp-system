<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Player;
use SvenHK\Maerquin\Entity\User;
use SvenHK\Maerquin\Exception\MaerquinUserAlreadyExists;
use SvenHK\Maerquin\Model\Player as PlayerModel;
use SvenHK\Maerquin\Model\User as UserModel;
use SvenHK\Maerquin\Repository\PlayerRepository;
use SvenHK\Maerquin\Repository\UserRepository;

readonly class PlayerFormHandler
{
    /**
     * @var PlayerRepository
     */
    private EntityRepository $playerRepository;

    /**
     * @var UserRepository
     */
    private EntityRepository $userRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->playerRepository = $entityManager->getRepository(Player::class);
    }

    public function handle(PlayerModel $player, Request $request): void
    {
        $formResolver = FormResolver::createFromRequest($request);

        $name = $formResolver->getValue('name', 'player');

        if (trim($name) === '') {
            $name = '(Naamloos)';
        }

        $player->updatePlayer(
            $name,
            $formResolver->getValue('email', 'player'),
        );

        $coupledAccount = $this->userRepository->findByPlayer($player->getId());
        $coupledAccountUsername = $formResolver->getValue('accountName', 'player', '');

        if ($coupledAccount === null && $coupledAccountUsername !== '') {
            $coupledAccount = UserModel::create($player);

            $this->userRepository->save($coupledAccount);
        }

        if ($coupledAccount !== null) {
            $coupledAccount->demote();

            $this->updateUsername(
                $coupledAccount,
                $coupledAccountUsername,
            );

            if ($formResolver->getBoolean('isAdmin', 'player', false) === true) {
                $coupledAccount->promote();
            }

            $this->userRepository->save($coupledAccount);
        }

        $this->playerRepository->save($player);
    }

    private function updateUsername(UserModel $user, string $username): void
    {
        $existingAccount = $this->userRepository->findByUsername($username);

        if ($existingAccount && $existingAccount->getId() !== $user->getId()) {
            throw MaerquinUserAlreadyExists::withUsername($username);
        }

        $user->changeUsername($username);
    }
}
