<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Player;
use SvenHK\Maerquin\Entity\User;
use SvenHK\Maerquin\Model\Player as PlayerModel;
use SvenHK\Maerquin\Repository\PlayerRepository;
use SvenHK\Maerquin\Repository\UserRepository;

class PlayerFormHandler
{
    /**
     * @var PlayerRepository
     */
    private readonly EntityRepository $playerRepository;

    /**
     * @var UserRepository
     */
    private readonly EntityRepository $userRepository;

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

        if ($coupledAccount !== null) {
            $coupledAccount->demote();

            if ($formResolver->getBoolean('isAdmin', 'player') === true) {
                $coupledAccount->promote();
            }
        }

        $this->userRepository->save($coupledAccount);
        $this->playerRepository->save($player);
    }
}
