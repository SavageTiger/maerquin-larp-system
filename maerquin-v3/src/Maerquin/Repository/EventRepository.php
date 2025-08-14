<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Event;

class EventRepository extends EntityRepository
{
    /**
     * @return Event[]
     */
    public function findAllSorted(): array
    {
        return $this->findBy([], ['startDate' => 'DESC']);
    }

    public function save(Event $event): void
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }
}
