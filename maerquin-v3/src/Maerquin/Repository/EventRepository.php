<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\CharacterEventLink;
use SvenHK\Maerquin\Entity\Event;

class EventRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function findById(string $eventId): null | Event
    {
        return $this->entities()->find($eventId);
    }

    /**
     * @return Event[]
     */
    public function findAll(): array
    {
        return $this->entities()->findAll();
    }

    /**
     * @return Event[]
     */
    public function findAllSorted(): array
    {
        return $this->entities()->findBy([], ['startDate' => 'DESC']);
    }

    /**
     * @return Event[]
     */
    public function findAllForCharacter(string $characterId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('event')
            ->from(Event::class, 'event')
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

    /**
     * @return Event[]
     */
    public function findAllForForm(string $formId): array
    {
        return $this->entities()->findBy(['form' => $formId], ['startDate' => 'ASC']);
    }

    public function save(Event $event): void
    {
        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }

    /**
     * @return EntityRepository<Event>
     */
    private function entities(): EntityRepository
    {
        return $this->entityManager->getRepository(Event::class);
    }
}
