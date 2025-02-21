<?php

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use SvenHK\Maerquin\Entity\RaceSkillLink;
use SvenHK\Maerquin\Entity\Skill as Skill;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Model\Skill as SkillModel;

class SkillRepository extends EntityRepository
{
    public function findById(string $skillId) : ?SkillModel
    {
        return $this->find($skillId);
    }

    /**
     * @return SkillModel[]
     */
    public function findAllSorted() : array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    /**
     * @return SkillModel[]
     */
    public function findAllMandatorySortedForRace(string $raceId) : array
    {
        $skillIds = array_column(
            $this->createBaseRaceSkillQuery($raceId)
                ->andWhere('skillLink.mandatory = :mandatory')
                ->setParameter('mandatory', true)
                ->getQuery()
                ->getArrayResult(),
            'id'
        );

        return $this->findBy(['id' => $skillIds], ['name' => 'ASC']);
    }

    private function createBaseRaceSkillQuery(string $raceId) : QueryBuilder
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('skill.id')
            ->from(Skill::class, 'skill')
            ->innerJoin(RaceSkillLink::class, 'skillLink', Join::WITH, 'skillLink.skill = skill.id AND skillLink.race = :raceId')
            ->where('skillLink.race = :raceId')
            ->setParameter('raceId', $raceId);
    }

    /**
     * @return SkillModel[]
     */
    public function findAllForbiddenSortedForRace(string $raceId) : array
    {
        $skillIds = array_column(
            $this->createBaseRaceSkillQuery($raceId)
                ->andWhere('skillLink.forbidden = :forbidden')
                ->setParameter('forbidden', true)
                ->getQuery()
                ->getArrayResult(),
            'id'
        );

        return $this->findBy(['id' => $skillIds], ['name' => 'ASC']);
    }

    /**
     * @throws MaerquinEntityNotFoundException
     */
    public function getById(string $skillId) : SkillModel
    {
        return $this->findOneBy(['id' => $skillId]) ?? throw MaerquinEntityNotFoundException::withType(Skill::class);
    }

    public function save(SkillModel $skill) : void
    {
        $this->getEntityManager()->persist($skill);
        $this->getEntityManager()->flush();
    }
}

