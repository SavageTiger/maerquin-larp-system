<?php

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Model\Element;

class ElementRepository extends EntityRepository
{
    /**
     * @return Element[]
     */
    public function findAllSorted() : array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    public function findById(string $elementId) : ?Element
    {
        return $this->find($elementId);
    }
}

