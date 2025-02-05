<?php

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Model\Deity;

class DeityRepository extends EntityRepository
{
    /**
     * @return Deity[]
     */
    public function findAllSorted(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }
}

