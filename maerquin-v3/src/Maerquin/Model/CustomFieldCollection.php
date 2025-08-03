<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use SvenHK\Maerquin\Entity\CustomField;

class CustomFieldCollection
{
    /**
     * @var CustomField[]
     */
    private array $customFields;

    /**
     * @var array<string, string>
     */
    private array $customFieldValues;

    public function __construct(array $customFields, array $customFieldValues)
    {
        $this->customFields = $customFields;
        $this->customFieldValues = $customFieldValues;
    }

    public function serialize(): array
    {
        foreach ($this->customFields as $customField) {
            $serialized[] = array_merge([
                'value' => $this->customFieldValues[$customField->getId()],
            ], $customField->serialize());
        }

        return $serialized;
    }
}
