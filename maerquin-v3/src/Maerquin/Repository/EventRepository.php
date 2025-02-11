<?php

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Event;

class EventRepository extends EntityRepository
{
    /**
     * @return Event[]
     */
    public function findAllSorted() : array
    {
        return $this->findBy([], ['startDate' => 'DESC']);
    }
}

