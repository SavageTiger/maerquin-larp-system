<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Deity;

class DeityRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return Deity[]
     */
    public function findAllSorted(): array
    {
        return $this->entities()->findBy([], ['name' => 'ASC']);
    }

    /**
     * @return Deity[]
     */
    public function findAll(): array
    {
        return $this->entities()->findAll();
    }

    public function findById(string $deityId): null | Deity
    {
        return $this->entities()->find($deityId);
    }

    /**
     * @return EntityRepository<Deity>
     */
    private function entities(): EntityRepository
    {
        return $this->entityManager->getRepository(Deity::class);
    }
}
