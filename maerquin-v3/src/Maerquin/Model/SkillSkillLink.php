<?php

namespace SvenHK\Maerquin\Model;

class SkillSkillLink
{
    protected Skill $firstSkill;

    public function requiredSkillId(): string
    {
        return $this->firstSkill->getId();
    }

}

