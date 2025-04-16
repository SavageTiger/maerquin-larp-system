<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Skill;

class SkillLink
{
    protected UuidInterface $id;
    protected Skill $skill;
    protected int $points;
    protected int $amount;

    public function getId() : string
    {
        return $this->id->toString();
    }

    public function getSkill() : Skill
    {
        return $this->skill;
    }

    public function getPoints() : int
    {
        return $this->points;
    }

    public function getAmount() : int
    {
        return $this->amount;
    }
}
