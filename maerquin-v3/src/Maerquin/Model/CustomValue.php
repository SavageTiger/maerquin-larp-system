<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\CustomField;
use SvenHK\Maerquin\Entity\CustomValue as CustomValueEntity;

class CustomValue
{
    protected UuidInterface $id;
    protected CustomField $customField;
    protected string $entityId;
    protected string $value;

    public static function create(
        string $entityId,
        CustomField $customField,
        string $value,
    ): self {
        $customValue = new CustomValueEntity();
        $customValue->customField = $customField;
        $customValue->value = $value;
        $customValue->entityId = $entityId;

        return $customValue;
    }
}
