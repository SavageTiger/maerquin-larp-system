<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use SvenHK\Maerquin\Entity\Form;

class FormRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function save(Form $form): void
    {
        $this->entityManager->persist($form);
        $this->entityManager->flush();
    }
}
