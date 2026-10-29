<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Form;
use SvenHK\Maerquin\Exception\Persistence\EntityNotFoundException;

class FormRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getById(string $formId): Form
    {
        return $this->entities()->findOneBy(['id' => $formId]) ??
            throw EntityNotFoundException::withType(Form::class);
    }

    public function findById(string $formId): null | Form
    {
        return $this->entities()->findOneBy(['id' => $formId]);
    }

    /**
     * @return Form[]
     */
    public function findAllSorted(): array
    {
        return $this->entities()->findBy([], ['name' => 'ASC']);
    }

    public function save(Form $form): void
    {
        $this->entityManager->persist($form);
        $this->entityManager->flush();
    }

    public function delete(Form $form): void
    {
        $this->entityManager->remove($form);
        $this->entityManager->flush();
    }

    /**
     * @return EntityRepository<Form>
     */
    private function entities(): EntityRepository
    {
        return $this->entityManager->getRepository(Form::class);
    }
}
