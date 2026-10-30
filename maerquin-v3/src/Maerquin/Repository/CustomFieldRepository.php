<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use SvenHK\Maerquin\Model\CustomFieldCollection;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\CustomField;
use SvenHK\Maerquin\Entity\CustomValue;

class CustomFieldRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return CustomField[]
     */
    public function findForCharacter(): array
    {
        return $this->entities()->findBy(['tableName' => 'character'], ['ordinal' => 'ASC']);
    }

    public function findCollectionForCharacter(string $characterId): CustomFieldCollection
    {
        $customFields = $this->findForCharacter();
        $customValues = [];

        foreach ($customFields as $customField) {
            $customValues[$customField->getId()] = $this->readFieldValue($customField->getId(), $characterId);
        }

        return new CustomFieldCollection($customFields, $customValues);
    }

    public function updateFieldValue(string $fieldId, string $subjectId, string $value): void
    {
        $subjectField = $this->entities()->find($fieldId);

        if ($subjectField === null) {
            return;
        }

        $existingCustomValue = $this->entityManager->getRepository(CustomValue::class)
            ->findOneBy([
                'customField' => $fieldId,
                'entityId' => $subjectId,
            ]);

        if ($existingCustomValue !== null) {
            $this->entityManager->remove($existingCustomValue);
        }

        $this->entityManager->persist(CustomValue::create(
            $subjectId,
            $subjectField,
            $value,
        ));

        $this->entityManager->flush();
    }

    public function readFieldValue(string $fieldId, string $subjectId): string
    {
        $value = $this->entityManager->createQueryBuilder()
            ->select('c.value')
            ->from(CustomValue::class, 'c')
            ->where('c.customField = :fieldId AND c.entityId = :subjectId')
            ->setParameter('fieldId', $fieldId)
            ->setParameter('subjectId', $subjectId)
            ->getQuery()
            ->getArrayResult();

        return $value[0]['value'] ?? '';
    }

    /**
     * @return EntityRepository<CustomField>
     */
    private function entities(): EntityRepository
    {
        return $this->entityManager->getRepository(CustomField::class);
    }
}
