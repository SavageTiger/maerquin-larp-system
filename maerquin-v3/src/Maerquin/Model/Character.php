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
    protected string $guild;
    protected string $title;
    protected string $occupation;
    protected string $birthplace;
    protected string $notes;

    /**
     * @var SkillLink[]
     */
    protected Collection $skills;

    public function serialize(bool $compact): array
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

        foreach ($this->getSkills() as $skillCoupling) {
            $linkedSkill = $skillCoupling->getSkill()->serializeAsLinked();

            $linkedSkills[] = array_merge(
                $linkedSkill,
                [
                    'points' => $skillCoupling->getPoints(),
                    'numberOfTimes' => $skillCoupling->getAmount(),
                ],
            );
        }

        return array_merge(
            $minimal,
            [
                'guild' => $this->getGuild(),
                'occupation' => $this->getOccupation(),
                'birthplace' => $this->getBirthplace(),
                'isDeceased' => $this->isDeceased(),
                'notes' => $this->getNotes(),
                'raceId' => $this->race->getId(),
                'linkedSkills' => $linkedSkills,
            ],
        );
    }

    public function getId(): string
    {
        return (string)$this->id;
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

    public function getSecondaryDeityId(): string
    {
        return $this->secondaryDeity?->getId() ?? '';
    }

    public function playerId(): null | string
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

    public function getGuild(): string
    {
        return $this->guild;
    }

    public function getOccupation(): string
    {
        return $this->occupation;
    }

    public function getBirthplace(): string
    {
        return $this->birthplace;
    }

    private function isDeceased(): bool
    {
        return $this->deceased;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function updateCharacter(
        string $name,
        null | Player $player,
        Race $race,
        bool $isDeceased,
        null | Deity $primaryDeity,
        null | Deity $secondaryDeity,
        string $guild,
        string $title,
        string $occupation,
        string $birthplace,
        string $notes,
    ): void {
        $this->name = $name;
        $this->player = $player;
        $this->race = $race;
        $this->deceased = $isDeceased;
        $this->primaryDeity = $primaryDeity;
        $this->secondaryDeity = $secondaryDeity;
        $this->guild = $guild;
        $this->title = $title;
        $this->occupation = $occupation;
        $this->birthplace = $birthplace;
        $this->notes = $notes;
    }
}
