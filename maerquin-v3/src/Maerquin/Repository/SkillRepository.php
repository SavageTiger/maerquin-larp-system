<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Skill;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Model\Skill as SkillModel;

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
