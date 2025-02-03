<?php

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\UuidInterface;

class Skill
{
    protected UuidInterface $id;
    protected string $name;
    protected ?Deity $deity;
    protected ?Element $element;
    protected ?SkillType $skillType;

    public function serialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'deityName' => $this->getDeityName(),
            'elementName' => $this->getElementName(),
            'typeName' => $this->getTypeName()
        ];
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDeityName(): string
    {
        return $this->deity?->getName() ?? '';
    }

    public function getElementName(): string
    {
        return $this->element?->getName() ?? '';
    }

    public function getTypeName(): string
    {
        return $this->skillType?->getName() ?? '';
    }
}

