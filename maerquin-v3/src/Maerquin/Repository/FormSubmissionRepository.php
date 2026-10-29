<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\FormSubmission;

class FormSubmissionRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return FormSubmission[]
     */
    public function findAllForForm(string $formId): array
    {
        return $this->entities()->findBy(['form' => $formId]);
    }

    /**
     * @return EntityRepository<FormSubmission>
     */
    private function entities(): EntityRepository
    {
        return $this->entityManager->getRepository(FormSubmission::class);
    }
}
