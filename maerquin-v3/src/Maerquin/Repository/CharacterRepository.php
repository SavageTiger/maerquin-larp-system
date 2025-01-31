<?php

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Character;

class CharacterRepository extends EntityRepository
{
    /**
     * @return Character[]
     */
    public function findAllSorted(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    /**
     * @return Character[]
     */
    public function forPlayer(string $playerId): array
    {
        return $this->findBy(['player' => $playerId], ['name' => 'ASC']);
    }
}

