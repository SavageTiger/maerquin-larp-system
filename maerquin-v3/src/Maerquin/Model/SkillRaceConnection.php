<?php

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Skill as SkillEntity;

class SkillRaceConnection
{
    protected UuidInterface $id;
    protected string $name;
    protected bool $mandatory;
    protected bool $forbidden;
    protected int $points;
    protected SkillEntity $skill;

    public function serialize() : array
    {
        return [
            'id' => $this->getId(),
            'skillName' => $this->skill->getName(),
            'mandatory' => $this->isMandatory(),
            'forbidden' => $this->isForbidden(),
            'customPoints' => $this->getCustomPoints(),
        ];
    }

    public function getId() : string
    {
        return $this->id->toString();
    }

    public function isMandatory() : bool
    {
        return $this->mandatory;
    }

    public function isForbidden() : bool
    {
        return $this->forbidden;
    }

    public function getCustomPoints() : int
    {
        return $this->points;
    }
}

