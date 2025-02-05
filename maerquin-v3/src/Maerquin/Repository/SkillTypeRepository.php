<?php

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Model\SkillType;

class SkillTypeRepository extends EntityRepository
{
    /**
     * @return SkillType[]
     */
    public function findAllSorted() : array
    {
        return $this->findBy([], ['ordinal' => 'ASC']);
    }

    public function getOneById(string $skillTypeId) : SkillType
    {
        return $this->findOneBy(['id' => $skillTypeId]);
    }
}

