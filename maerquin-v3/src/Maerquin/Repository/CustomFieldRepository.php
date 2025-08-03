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
