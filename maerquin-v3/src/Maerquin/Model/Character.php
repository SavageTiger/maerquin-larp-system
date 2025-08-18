<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Deity;

class Character
{
    public const BASE_XP = 100;
    public const MAX_XP = 800;

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
     * @param array<int, string> $warnings
     */
    protected array $warnings = [];

    /**
     * @var Collection<SkillLink>
     */
    protected Collection $skills;

    public static function createWithDefaults(
        UuidInterface $characterId,
        Race $race,
    ): self {
        $character = new static();
        $character->id = $characterId;

        $character->updateWarnings([]);

        $linkedSkills = [];

        foreach ($race->getMandatorySkills()->getSkills() as $mandatorySkill) {
            $linkedSkills[] = SkillLink::create(
                skill: $mandatorySkill,
                character: $character,
                points: $race->getCustomPointsForSkill($mandatorySkill),
                amount: 1,
                fastCasting: false,
                armouredCasting: false,
            );
        }

        $character->updateCharacter(
            name: '',
            player: null,
            race: $race,
            isDeceased: false,
            primaryDeity: null,
            secondaryDeity: null,
            guild: '',
            title: '',
            occupation: '',
            birthplace: '',
            notes: '',
            skillLinkCollection: new SkillLinkCollection($linkedSkills),
        );

        return $character;
    }

    /**
     * @param array<int, string> $warnings
     */
    public function updateWarnings(array $warnings): void
    {
        $this->warnings = $warnings;
    }

    /**
     * @return SkillLink[]
     */
    public function getSkills(): array
    {
        return $this->skills->toArray();
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
        SkillLinkCollection $skillLinkCollection,
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

        $this->skills = new ArrayCollection(
            $skillLinkCollection->getSkillLinks(),
        );
    }

    public function serialize(bool $compact): array
    {
        $minimal = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'primaryDeityId' => $this->getPrimaryDeityId(),
            'secondaryDeityId' => $this->getSecondaryDeityId(),
            'playerId' => $this->playerId(),
            'hasWarnings' => count($this->getWarnings()) > 0,
        ];

        if ($compact === true) {
            return $minimal;
        }

        $linkedSkills = [];

        foreach ($this->getSkills() as $skillCoupling) {
            $linkedSkill = $skillCoupling->getSkill()->serializeAsLinked(
                $skillCoupling->getPoints(),
            );

            $linkedSkills[] = array_merge(
                $linkedSkill,
                [
                    'numberOfTimes' => $skillCoupling->getAmount(),
                    'fastCasting' => $skillCoupling->hasFastCasting(),
                    'armouredCasting' => $skillCoupling->hasArmouredCasting(),
                    'buyableAmount' => $skillCoupling->getSkill()->getMaximumAmountBuyable(),
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
     * @return array<int, string>
     */
    public function getWarnings(): array
    {
        return $this->warnings;
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

    public function getRace(): Race
    {
        return $this->race;
    }

    public function hasSkill(Skill | string $skill): bool
    {
        if (is_string($skill) === true) {
            $skillId = $skill;
        } else {
            $skillId = $skill->getId();
        }

        return array_find(
            $this->getSkills(),
            fn(SkillLink $skillLink): bool => $skillLink->getSkill()->getId() === $skillId,
        ) !== null;
    }

    public function spendPoints(): float
    {
        $spendPoints = 0;

        foreach ($this->skills as $skillLink) {
            $spendPoints += $skillLink->getPoints() * $skillLink->getAmount();
        }

        return $spendPoints;
    }
}
