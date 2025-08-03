<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Deity;

class Character
{
    protected UuidInterface $id;
    protected string $name;
    protected null | Deity $primaryDeity;
    protected null | Deity $secondaryDeity;
    protected null | Player $player;
    protected Race $race;
    protected bool $deceased;

    /**
     * @var SkillLink[]
     */
    protected Collection $skills;

    public function serialize($compact): array
    {
        $minimal = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'primaryDeityId' => $this->getPrimaryDeityId(),
            'secondaryDeityId' => $this->getSecondaryDeityId(),
            'playerId' => $this->playerId(),
        ];

        if ($compact === true) {
            return $minimal;
        }

        $linkedSkills = [];

        foreach ($this->getSkills() as $linkedSkill) {
            $linkedSkills[] = [
                'skillName' => $linkedSkill->getSkill()->getName(),
                'skillGroup' => $linkedSkill->getSkill()->getSkillTypeName(),
                'skillGroupOrdering' => $linkedSkill->getSkill()->getSkillTypeOrdering(),
                'numberOfTimes' => $linkedSkill->getAmount(),
                'points' => $linkedSkill->getPoints(),
                'numberOfTimesSource' => $linkedSkill->getSkill()->getNumberOfTimes(),
                'pointsSource' => $linkedSkill->getSkill()->getPoints(),
            ];
        }

        return array_merge(
            $minimal,
            [
                'isDeceased' => $this->isDeceased(),
                'raceId' => $this->race->getId(),
                'linkedSkills' => $linkedSkills,
            ],
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPrimaryDeityId(): string
    {
        return $this->primaryDeity?->getId() ?? '';
    }

    public function getSecondaryDeityId()
    {
        return $this->secondaryDeity?->getId() ?? '';
    }

    public function playerId(): string
    {
        return $this->player?->getId();
    }

    /**
     * @return SkillLink[]
     */
    public function getSkills(): array
    {
        return $this->skills->toArray();
    }

    private function isDeceased(): bool
    {
        return $this->deceased;
    }
}
