<?php

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Model\Skill;

class SkillRepository extends EntityRepository
{
    public function findById(string $skillId) : ?Skill
    {
        return $this->find($skillId);
    }

    /**
     * @return Skill[]
     */
    public function findAllSorted() : array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    /**
     * @throws MaerquinEntityNotFoundException
     */
    public function getById(string $skillId) : Skill
    {
        return $this->findOneBy(['id' => $skillId]) ?? throw MaerquinEntityNotFoundException::withType(Skill::class);
    }

    public function save(Skill $skill) : void
    {
        $this->getEntityManager()->persist($skill);
        $this->getEntityManager()->flush();
    }
}

