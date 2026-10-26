<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\SkillType;

class SkillTypeRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function getOneById(string $skillTypeId): SkillType
    {
        return $this->entities()->findOneBy(['id' => $skillTypeId]);
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
        return $this->entities()->findBy([], ['ordinal' => 'ASC']);
    }

    /**
     * @return EntityRepository<SkillType>
     */
    private function entities(): EntityRepository
    {
        return $this->entityManager->getRepository(SkillType::class);
    }
}
