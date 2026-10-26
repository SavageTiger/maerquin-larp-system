<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Skill;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Model\Skill as SkillModel;

class SkillRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function findById(string $skillId): null | Skill
    {
        return $this->entities()->find($skillId);
    }

    /**
     * @return Skill[]
     */
    public function findAllSorted(): array
    {
        return $this->entities()->findBy([], ['name' => 'ASC']);
    }

    /**
     * @throws MaerquinEntityNotFoundException
     */
    public function getById(string $skillId): Skill
    {
        return $this->entities()->findOneBy(['id' => $skillId]) ?? throw MaerquinEntityNotFoundException::withType(Skill::class);
    }

    public function save(SkillModel $skill): void
    {
        $this->entityManager->persist($skill);
        $this->entityManager->flush();
    }

    /**
     * @return EntityRepository<Skill>
     */
    private function entities(): EntityRepository
    {
        return $this->entityManager->getRepository(Skill::class);
    }
}
