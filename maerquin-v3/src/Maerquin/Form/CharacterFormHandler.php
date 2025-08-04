<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\Player;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\PlayerRepository;

class CharacterFormHandler
{
    /**
     * @var CharacterRepository
     */
    private EntityRepository $characterRepository;

    /**
     * @var PlayerRepository
     */
    private EntityRepository $playerRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->characterRepository = $entityManager->getRepository(Character::class);
        $this->playerRepository = $entityManager->getRepository(Player::class);
    }

    public function handle(string $characterId, Request $request): void
    {
        $formResolver = FormResolver::createFromRequest($request);

        $character = $this->characterRepository->getById($characterId);

        $player = $this->playerRepository->getById(
            $formResolver->getValue('playerId', 'character'),
        );

        $character->updateCharacter(
            $formResolver->getValue('name', 'character'),
            $player,
        );

        $this->characterRepository->save($character);
    }
}
