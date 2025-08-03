<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use SvenHK\Maerquin\Entity\RaceSkillLink;
use SvenHK\Maerquin\Entity\Skill;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Model\Skill as SkillModel;
use SvenHK\Maerquin\Model\SkillCollection;

class SkillRepository extends EntityRepository
{
    public function findById(string $skillId): null | SkillModel
    {
        return $this->find($skillId);
    }

    /**
     * @return SkillModel[]
     */
    public function findAllSorted(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    /**
     * @return SkillCollection[]
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
     * @return SkillCollection[]
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
     * @return SkillCollection[]
     */
    public function findDifferentPointSkillsSortedForRace(string $raceId): array
    {
        return $this->createBaseRaceSkillConnectionQuery($raceId)
            ->andWhere('skillRaceConnection.points > 0')
            ->andWhere('skillRaceConnection.forbidden = false')
            ->andWhere('skillRaceConnection.mandatory = false')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws MaerquinEntityNotFoundException
     */
    public function getById(string $skillId): SkillModel
    {
        return $this->findOneBy(['id' => $skillId]) ?? throw MaerquinEntityNotFoundException::withType(Skill::class);
    }

    public function save(SkillModel $skill): void
    {
        $this->getEntityManager()->persist($skill);
        $this->getEntityManager()->flush();
    }
}
