<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Entity\RaceSkillLink;
use SvenHK\Maerquin\Exception\Persistence\EntityNotFoundException;

class RaceRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getById(string $raceId): Race
    {
        return $this->entities()->findOneBy(['id' => $raceId]) ??
            throw EntityNotFoundException::withType(Race::class);
    }

    public function findById(string $raceId): null | Race
    {
        return $this->entities()->findOneBy(['id' => $raceId]);
    }

    public function findByName(string $name): null | Race
    {
        return $this->entities()->findOneBy(['name' => $name]);
    }

    /**
     * @return array<int, RaceSkillLink>
     */
    public function findAllMandatorySortedForRace(string $raceId): array
    {
        return $this->createBaseRaceSkillConnectionQuery($raceId)
            ->andWhere('skillRaceConnection.mandatory = :mandatory')
            ->andWhere('skillRaceConnection.points = 0')
            ->setParameter('mandatory', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, RaceSkillLink>
     */
    public function findAllForbiddenSortedForRace(string $raceId): array
    {
        return $this->createBaseRaceSkillConnectionQuery($raceId)
            ->andWhere('skillRaceConnection.forbidden = :forbidden')
            ->andWhere('skillRaceConnection.points = 0')
            ->setParameter('forbidden', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, RaceSkillLink>
     */
    public function findDifferentPointSkillsSortedForRace(string $raceId): array
    {
        return $this->createBaseRaceSkillConnectionQuery($raceId)
            ->andWhere('skillRaceConnection.points > -1')
            ->andWhere('skillRaceConnection.forbidden = false')
            ->andWhere('skillRaceConnection.mandatory = false')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Race[]
     */
    public function findAllSorted(): array
    {
        return $this->entities()->findBy([], ['name' => 'ASC']);
    }

    public function save(Race $race): void
    {
        $this->entityManager->persist($race);
        $this->entityManager->flush();
    }

    private function createBaseRaceSkillConnectionQuery(string $raceId): QueryBuilder
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('skillRaceConnection')
            ->from(RaceSkillLink::class, 'skillRaceConnection')
            ->where('skillRaceConnection.race = :raceId')
            ->setParameter('raceId', $raceId)
            ->addOrderBy('skillRaceConnection.skill');
    }

    /**
     * @return EntityRepository<Race>
     */
    private function entities(): EntityRepository
    {
        return $this->entityManager->getRepository(Race::class);
    }
}
