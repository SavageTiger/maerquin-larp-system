<?php

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Model\Skill;

class SkillRepository extends EntityRepository
{
    /**
     * @return Skill[]
     */
    public function findAllSorted(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }
}

