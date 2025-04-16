<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\CharacterEventLink;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;

class CharacterRepository extends EntityRepository
{
    /**
     * @return Character[]
     */
    public function findAllSorted() : array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    /**
     * @return Character[]
     */
    public function forPlayer(string $playerId) : array
    {
        return $this->findBy(['player' => $playerId], ['name' => 'ASC']);
    }

    /**
     * @throws MaerquinEntityNotFoundException
     */
    public function getById(string $characterId) : Character
    {
        return $this->findOneBy(['id' => $characterId]) ?? throw MaerquinEntityNotFoundException::withType(Character::class);
    }

    /**
     * @return Character[]
     */
    public function findByEvent(string $eventId) : array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('c')
            ->distinct('c')
            ->from(Character::class, 'c')
            ->leftJoin(CharacterEventLink::class, 'cl', Join::WITH, 'c.id = cl.character and cl.event = :eventId')
            ->where('cl.event = :eventId')
            ->setParameter('eventId', $eventId);

        return $qb->getQuery()->getResult();
    }
}
