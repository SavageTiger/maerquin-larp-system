<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Entity\RaceSkillLink;

class RaceRepository extends EntityRepository
{
    public function getById(string $raceId): Race
    {
        return $this->findOneBy(['id' => $raceId]);
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

    private function createBaseRaceSkillConnectionQuery(string $raceId): QueryBuilder
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('skillRaceConnection')
            ->from(RaceSkillLink::class, 'skillRaceConnection')
            ->where('skillRaceConnection.race = :raceId')
            ->setParameter('raceId', $raceId);
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
        return $this->findBy([], ['name' => 'ASC']);
    }

    public function save(Race $race): void
    {
        /** @var CharacterRepository $characterRepository */
        $characterRepository = $this->getEntityManager()->getRepository(Character::class);
        $characterRepository->findAllByRace($race->getId());

        foreach ($characterRepository->findAllByRace($race->getId()) as $characters) {
            $characterRepository->save($characters);
        }

        $this->getEntityManager()->persist($race);
        $this->getEntityManager()->flush();
    }
}
