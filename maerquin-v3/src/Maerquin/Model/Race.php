<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\RaceSkillLink;

class Race
{
    protected UuidInterface $id;
    protected string $name;

    /**
     * @var Collection<RaceSkillLink>
     */
    protected Collection $skillConnections;

    public function updateRace(string $name, array $skillConnections): void
    {
        $this->name = $name;

        $this->skillConnections->clear();

        foreach ($skillConnections as $skillConnection) {
            $this->skillConnections->add($skillConnection);
        }
    }

    public function getMandatorySkills(): SkillCollection
    {
        $mandatorySkills = [];

        foreach ($this->skillConnections as $skillConnection) {
            if ($skillConnection->isMandatory() === true) {
                $mandatorySkills[] = $skillConnection->getSkill();
            }
        }

        return new SkillCollection($mandatorySkills);
    }

    public function getCustomPointsForSkill(Skill $skill): float
    {
        foreach ($this->skillConnections as $skillConnection) {
            if (
                $skillConnection->getSkill()->getId() === $skill->getId() &&
                $skillConnection->isCustomPoints() === true
            ) {
                return $skillConnection->getCustomPoints();
            }
        }

        return $skill->getPoints();
    }

    public function getId(): string
    {
        return (string)$this->id;
    }

    public function serialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }
}
