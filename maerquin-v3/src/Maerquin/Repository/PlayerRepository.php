<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Model\Player;

class PlayerRepository extends EntityRepository
{
    public function save(Player $player): void
    {
        $this->getEntityManager()->persist($player);
        $this->getEntityManager()->flush();
    }

    public function getById(string $playerId): Player
    {
        return $this->findOneBy(['id' => $playerId]) ?? throw MaerquinEntityNotFoundException::withType(Player::class);
    }

    public function findById(string $playerId): null | Player
    {
        return $this->findOneBy(['id' => $playerId]);
    }

    /**
     * @return Player[]
     */
    public function findAllSorted(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }
}
