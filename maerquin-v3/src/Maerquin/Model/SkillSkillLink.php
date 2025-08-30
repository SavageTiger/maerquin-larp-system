<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\SkillSkillLink as SkillSkillLinkEntity;

class SkillSkillLink
{
    protected UuidInterface $id;
    protected Skill $firstSkill;
    protected Skill $secondSkill;
    protected string $requirementType;

    private function __construct(
        Skill $subjectSkill,
        Skill $requiredSkill,
        RequirementType $requirementType,
    ) {
        $this->id = Uuid::uuid4();
        $this->firstSkill = $requiredSkill;
        $this->secondSkill = $subjectSkill;
        $this->requirementType = $requirementType->value;
    }

    public static function createForSkill(
        Skill $subjectSkill,
        Skill $requiredSkill,
        RequirementType $requirementType,
    ): self {
        return new SkillSkillLinkEntity(
            $subjectSkill,
            $requiredSkill,
            $requirementType,
        );
    }

    public function requiredSkillId(): string
    {
        return $this->firstSkill->getId();
    }

    public function getId(): string
    {
        return (string)$this->id;
    }

    public function getRequirementType(): RequirementType
    {
        return RequirementType::from($this->requirementType);
    }
}
