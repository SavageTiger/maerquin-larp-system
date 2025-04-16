<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Race;

class RaceRepository extends EntityRepository
{
    public function getById(string $raceId) : Race
    {
        return $this->findOneBy(['id' => $raceId]);
    }

    /**
     * @return Race[]
     */
    public function findAllSorted() : array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    public function save(Race $race) : void
    {
        $this->getEntityManager()->persist($race);
        $this->getEntityManager()->flush();
    }
}
