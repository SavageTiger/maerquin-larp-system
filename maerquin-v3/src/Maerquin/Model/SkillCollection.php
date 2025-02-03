<?php

namespace SvenHK\Maerquin\Model;

class SkillCollection
{
    /**
     * @var Skill[]
     */
    private array $skills;

    public function __construct(array $skills)
    {
        $this->skills = $skills;
    }

    public function serialize(): array
    {
        return array_map(
            fn(Skill $skill) => $skill->serialize(),
            $this->skills
        );
    }
}

