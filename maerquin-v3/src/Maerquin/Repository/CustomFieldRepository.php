<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\CustomField;
use SvenHK\Maerquin\Entity\CustomValue;

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
        $em = $this->getEntityManager();

        $subjectField = $this->find($fieldId);

        if ($subjectField === null) {
            return;
        }

        $existingCustomValue = $em->getRepository(CustomValue::class)->findOneBy([
            'customField' => $fieldId,
            'entityId' => $subjectId,
        ]);

        if ($existingCustomValue !== null) {
            $em->remove($existingCustomValue);
        }

        $em->persist(CustomValue::create(
            $subjectId,
            $subjectField,
            $value,
        ));

        $em->flush();
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
