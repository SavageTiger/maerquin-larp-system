<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

class SkillTypeCollection
{
    /**
     * @var SkillType[]
     */
    private array $skillTypes;

    public function __construct(array $skillTypes)
    {
        $this->skillTypes = $skillTypes;
    }

    public function serialize(): array
    {
        return array_map(
            fn(SkillType $skillType) => $skillType->serialize(),
            $this->skillTypes,
        );
    }
}
