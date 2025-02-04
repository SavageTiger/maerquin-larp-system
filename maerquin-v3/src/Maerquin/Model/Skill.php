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
    protected int $points;
    protected int $numberOfTimes;
    protected string $description;
    protected string $remarks;
    protected string $distance;
    protected string $duration;
    protected bool $nonFree;
    protected bool $hidden;
    protected int $level;

    public function serialize(bool $compact)
    {
        $minimal = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'deityName' => $this->getDeityName(),
            'elementName' => $this->getElementName(),
            'typeName' => $this->getTypeName(),
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
            ]);
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

    public function getPoints(): int
    {
        return $this->points;
    }

    public function getNumberOfTimes(): int
    {
        return $this->numberOfTimes;
    }

    public function getDistance(): string
    {
        return $this->distance;
    }

    public function getDuration(): string
    {
        return $this->duration;
    }

    public function isFreelyAvailable(): bool
    {
        return $this->nonFree === false;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRemarks(): string
    {
        return $this->remarks;
    }
}
