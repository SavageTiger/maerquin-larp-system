<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\CharacterSkillLink;
use SvenHK\Maerquin\Entity\Skill;

class SkillLink
{
    protected UuidInterface $id;
    protected Skill $skill;
    protected Character $character;
    protected float $points;
    protected int $amount;
    protected bool $fastCasting;
    protected bool $armouredCasting;

    public static function create(
        Skill $skill,
        Character $character,
        float $points,
        int $amount,
        bool $fastCasting,
        bool $armouredCasting,
    ): CharacterSkillLink {
        $skillLink = new CharacterSkillLink();
        $skillLink->id = Uuid::uuid4();
        $skillLink->skill = $skill;
        $skillLink->character = $character;
        $skillLink->points = $points;
        $skillLink->amount = $amount;
        $skillLink->fastCasting = $fastCasting;
        $skillLink->armouredCasting = $armouredCasting;

        return $skillLink;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getSkill(): Skill
    {
        return $this->skill;
    }

    public function getPoints(): float
    {
        return $this->points;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function hasFastCasting(): bool
    {
        return $this->fastCasting;
    }

    public function hasArmouredCasting(): bool
    {
        return $this->armouredCasting;
    }
}
