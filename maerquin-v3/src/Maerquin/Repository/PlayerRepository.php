<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Player;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Model\Player as PlayerModel;

class PlayerRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function save(PlayerModel $player): void
    {
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    /**
     * @throws MaerquinEntityNotFoundException
     */
    public function getById(string $playerId): Player
    {
        return $this->entities()->findOneBy(['id' => $playerId]) ?? throw MaerquinEntityNotFoundException::withType(Player::class);
    }

    public function findById(string $playerId): null | Player
    {
        return $this->entities()->findOneBy(['id' => $playerId]);
    }

    public function findByEmail(string $email): null | Player
    {
        if (trim($email) === '') {
            return null;
        }

        return $this->entities()->findOneBy(['email' => $email]);
    }

    /**
     * @return Player[]
     */
    public function findAllSorted(): array
    {
        return $this->entities()->findBy([], ['name' => 'ASC']);
    }

    /**
     * @return EntityRepository<Player>
     */
    private function entities(): EntityRepository
    {
        return $this->entityManager->getRepository(Player::class);
    }
}
