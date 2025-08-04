<?php

declare(strict_types=1);

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

    public function serialize(bool $compact): array
    {
        return array_map(
            fn(Skill $skill) => $skill->serialize($compact),
            $this->skills,
        );
    }

    /**
     * @return array<int, Skill>
     */
    public function getSkills(): array
    {
        return $this->skills;
    }
}
