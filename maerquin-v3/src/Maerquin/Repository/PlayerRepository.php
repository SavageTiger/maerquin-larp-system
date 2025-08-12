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

    /**
     * @return Player[]
     */
    public function findAllSorted(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    /**
     * @throws MaerquinEntityNotFoundException
     */
    public function getById(string $userId): Player
    {
        return $this->findOneBy(['id' => $userId]) ?? throw MaerquinEntityNotFoundException::withType(Player::class);
    }
}
