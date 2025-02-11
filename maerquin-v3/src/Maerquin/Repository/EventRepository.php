<?php

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;

class EventRepository extends EntityRepository
{
    /**
     * @return Event[]
     */
    public function findAllSorted() : array
    {
        return $this->findBy([], ['startDate' => 'DESC']);
    }

    /**
     * @throws MaerquinEntityNotFoundException
     */
    public function getById(string $eventId) : Event
    {
        return $this->findOneBy(['id' => $eventId]) ?? throw MaerquinEntityNotFoundException::withType(Event::class);
    }
}

