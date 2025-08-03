<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Model\Player;

class PlayerRepository extends EntityRepository
{
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
