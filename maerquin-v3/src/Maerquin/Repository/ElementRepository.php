<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Element;

class ElementRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return Element[]
     */
    public function findAllSorted(): array
    {
        return $this->entities()->findBy([], ['name' => 'ASC']);
    }

    public function findById(string $elementId): null | Element
    {
        return $this->entities()->find($elementId);
    }

    /**
     * @return EntityRepository<Element>
     */
    private function entities(): EntityRepository
    {
        return $this->entityManager->getRepository(Element::class);
    }
}
