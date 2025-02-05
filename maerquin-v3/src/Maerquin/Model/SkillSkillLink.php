<?php

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\SkillSkillLink as SkillSkillLinkEntity;

class SkillSkillLink
{
    protected UuidInterface $id;
    protected Skill $firstSkill;
    protected Skill $secondSkill;

    private function __construct(Skill $subjectSkill, Skill $requiredSkill)
    {
        $this->id = Uuid::uuid4();
        $this->firstSkill = $requiredSkill;
        $this->secondSkill = $subjectSkill;
    }

    public static function createForSkill(Skill $subjectSkill, Skill $requiredSkill) : self
    {
        return new SkillSkillLinkEntity($subjectSkill, $requiredSkill);
    }

    public function requiredSkillId() : string
    {
        return $this->firstSkill->getId();
    }

    public function getId() : string
    {
        return $this->id;
    }
}

