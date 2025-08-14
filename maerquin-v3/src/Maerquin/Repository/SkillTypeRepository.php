<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Model\SkillType;

class SkillTypeRepository extends EntityRepository
{
    public function getOneById(string $skillTypeId): SkillType
    {
        return $this->findOneBy(['id' => $skillTypeId]);
    }

    public function getDefault(): SkillType
    {
        return current($this->findAllSorted());
    }

    /**
     * @return SkillType[]
     */
    public function findAllSorted(): array
    {
        return $this->findBy([], ['ordinal' => 'ASC']);
    }
}
