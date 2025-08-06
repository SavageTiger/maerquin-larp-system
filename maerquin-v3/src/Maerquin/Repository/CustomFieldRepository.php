<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\CustomField;
use SvenHK\Maerquin\Entity\CustomValue;
use Webmozart\Assert\Assert;

class CustomFieldRepository extends EntityRepository
{
    /**
     * @return CustomField[]
     */
    public function findForCharacter(): array
    {
        return $this->findBy(['tableName' => 'character'], ['ordinal' => 'ASC']);
    }

    public function updateFieldValue(string $fieldId, string $subjectId, string $value): void
    {
        $entityManager = $this->getEntityManager();

        $subjectField = $this->find($fieldId);

        if ($subjectField === null) {
            return;
        }

        Assert::isInstanceOf($subjectField, CustomField::class);

        $existingCustomValue = $entityManager->getRepository(CustomValue::class)
            ->findOneBy([
                'customField' => $fieldId,
                'entityId' => $subjectId,
            ]);

        if ($existingCustomValue !== null) {
            $entityManager->remove($existingCustomValue);
        }

        $entityManager->persist(CustomValue::create(
            $subjectId,
            $subjectField,
            $value,
        ));

        $entityManager->flush();
    }

    public function readFieldValue(string $fieldId, string $subjectId): string
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $value = $qb
            ->select('c.value')
            ->from(CustomValue::class, 'c')
            ->where('c.customField = :fieldId AND c.entityId = :subjectId')
            ->setParameter('fieldId', $fieldId)
            ->setParameter('subjectId', $subjectId)
            ->getQuery()
            ->getArrayResult();

        return $value[0]['value'] ?? '';
    }
}
