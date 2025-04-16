<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\UuidInterface;

class Skill
{
    protected UuidInterface $id;
    protected string $name;
    protected ?Deity $deity;
    protected ?Element $element;
    protected SkillType $skillType;
    protected int $points;
    protected int $numberOfTimes;
    protected string $description;
    protected string $remarks;
    protected string $distance;
    protected string $duration;
    protected bool $nonFree;
    protected bool $hidden;
    protected int $level;
    protected ?SkillSkillLink $requiredSkillLink = null;

    public function serialize(bool $compact)
    {
        $minimal = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'deityId' => $this->getDeityId(),
            'elementId' => $this->getElementId(),
            'deityName' => $this->getDeityName(),
            'elementName' => $this->getElementName(),
            'skillTypeId' => $this->getSkillTypeId(),
        ];

        if ($compact === true) {
            return $minimal;
        }

        return array_merge(
            $minimal,
            [
                'points' => $this->getPoints(),
                'numberOfTimes' => $this->getNumberOfTimes(),
                'distance' => $this->getDistance(),
                'duration' => $this->getDuration(),
                'freelyAvailable' => $this->isFreelyAvailable(),
                'hidden' => $this->isHidden(),
                'level' => $this->getLevel(),
                'description' => $this->getDescription(),
                'remarks' => $this->getRemarks(),
                'requiresParentSkill' => $this->getParentRequirementSkillId(),
            ],
        );
    }

    public function getId() : string
    {
        return $this->id->toString();
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getDeityId() : string
    {
        return $this->deity?->getId() ?? '';
    }

    public function getElementId() : string
    {
        return $this->element?->getId() ?? '';
    }

    public function getDeityName() : string
    {
        return $this->deity?->getName() ?? '';
    }

    public function getElementName() : string
    {
        return $this->element?->getName() ?? '';
    }

    public function getSkillTypeId() : string
    {
        return $this->skillType->getId();
    }

    public function getPoints() : int
    {
        return $this->points;
    }

    public function getNumberOfTimes() : int
    {
        return $this->numberOfTimes;
    }

    public function getDistance() : string
    {
        return $this->distance;
    }

    public function getDuration() : string
    {
        return $this->duration;
    }

    public function isFreelyAvailable() : bool
    {
        return $this->nonFree === false;
    }

    public function isHidden() : bool
    {
        return $this->hidden;
    }

    public function getLevel() : int
    {
        return $this->level;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getRemarks() : string
    {
        return $this->remarks;
    }

    public function getParentRequirementSkillId() : ?string
    {
        return $this->requiredSkillLink?->requiredSkillId() ?? null;
    }

    public function getSkillTypeName() : string
    {
        return $this->skillType->getName();
    }

    public function getSkillTypeOrdering() : int
    {
        return $this->skillType->getOrdering();
    }

    public function updateSkill(
        string $name,
        ?Deity $deity,
        ?Element $element,
        SkillType $skillType,
        ?SkillSkillLink $requiredSkillLink,
        int $points,
        int $numberOfTimes,
        int $level,
        string $distance,
        string $duration,
        bool $notFreelyAvailable,
        bool $isHidden,
        string $description,
        string $remarks,
    ) : void {
        $this->name = $name;
        $this->deity = $deity;
        $this->element = $element;
        $this->skillType = $skillType;
        $this->requiredSkillLink = $requiredSkillLink;
        $this->points = $points;
        $this->numberOfTimes = $numberOfTimes;
        $this->level = $level;
        $this->distance = $distance;
        $this->duration = $duration;
        $this->nonFree = $notFreelyAvailable;
        $this->hidden = $isHidden;
        $this->description = $description;
        $this->remarks = $remarks;
    }
}
