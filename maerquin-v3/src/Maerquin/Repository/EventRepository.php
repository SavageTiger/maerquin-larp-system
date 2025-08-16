<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\CharacterEventLink;
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

    /**
     * @return Event[]
     */
    public function findAllForCharacter(string $characterId): array
    {
        return $this->createQueryBuilder('event')
            ->innerJoin(
                CharacterEventLink::class,
                'eventLink',
                'WITH',
                'eventLink.event = event.id',
            )
            ->innerJoin('eventLink.character', 'character')
            ->andWhere('character.id = :characterId')
            ->orderBy('event.startDate')
            ->setParameter('characterId', $characterId)
            ->getQuery()
            ->getResult();
    }

    public function save(Event $event): void
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }
}
