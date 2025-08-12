<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Player;
use SvenHK\Maerquin\Model\Player as PlayerModel;
use SvenHK\Maerquin\Repository\PlayerRepository;

class PlayerFormHandler
{
    /**
     * @var PlayerRepository
     */
    private EntityRepository $playerRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->playerRepository = $entityManager->getRepository(Player::class);
    }

    public function handle(PlayerModel $player, Request $request): void
    {
        $formResolver = FormResolver::createFromRequest($request);

        $name = $formResolver->getValue('name', 'player');

        if (trim($name) === '') {
            $name = '(Naamloos)';
        }

        $player->updatePlayer($name);

        $this->playerRepository->save($player);
    }
}
