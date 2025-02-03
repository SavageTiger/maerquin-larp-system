<?php

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Skill;

class SkillLink
{
    protected UuidInterface $id;
    protected Skill $skill;

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getSkill(): Skill
    {
        return $this->skill;
    }
}

