<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\CharacterEventLink;
use SvenHK\Maerquin\Entity\CharacterSkillLink;
use SvenHK\Maerquin\Exception\Persistence\EntityNotFoundException;
use SvenHK\Maerquin\Model\Character as CharacterModel;

class CharacterRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function findById(string $characterId): null | Character
    {
        return $this->entities()->find($characterId);
    }

    /**
     * @return Character[]
     */
    public function findAllSorted(): array
    {
        return $this->entities()->findBy([], ['name' => 'ASC']);
    }

    /**
     * @return Character[]
     */
    public function forPlayer(string $playerId): array
    {
        return $this->entities()->findBy(['player' => $playerId], ['name' => 'ASC']);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getById(string $characterId): Character
    {
        return $this->entities()->findOneBy(['id' => $characterId]) ?? throw EntityNotFoundException::withType(Character::class);
    }

    /**
     * @return Character[]
     */
    public function findByEvent(string $eventId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->distinct()
            ->from(Character::class, 'c')
            ->leftJoin(CharacterEventLink::class, 'cl', Join::WITH, 'c.id = cl.character and cl.event = :eventId')
            ->where('cl.event = :eventId')
            ->orderBy('c.name')
            ->setParameter('eventId', $eventId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Character[]
     */
    public function findAllCoupledToSkill(string $skillId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->distinct()
            ->from(Character::class, 'c')
            ->leftJoin(CharacterSkillLink::class, 'sl', Join::WITH, 'c.id = sl.character')
            ->where('sl.skill = :skillId')
            ->orderBy('c.name')
            ->setParameter('skillId', $skillId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Character[]
     */
    public function findAllByRace(string $raceId): array
    {
        return $this->entities()->findBy(['race' => $raceId], ['name' => 'ASC']);
    }

    public function save(CharacterModel $character): void
    {
        $this->entityManager->persist($character);
        $this->entityManager->flush();
    }

    /**
     * @return EntityRepository<Character>
     */
    private function entities(): EntityRepository
    {
        return $this->entityManager->getRepository(Character::class);
    }
}
