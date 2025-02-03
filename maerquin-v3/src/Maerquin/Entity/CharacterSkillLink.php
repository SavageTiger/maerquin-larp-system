<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\SkillLink;

#[ORM\Entity]
class CharacterSkillLink extends SkillLink
{
    // FIX: Armored casting
    // FIX: Fast-casting (Innate casting)

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Character::class, inversedBy: "skills")]
    #[ORM\JoinColumn(nullable: false)]
    protected Character $character;

    #[ORM\ManyToOne(targetEntity: Skill::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected Skill $skill;

    #[ORM\Column(type: "integer")]
    protected int $points;

    #[ORM\Column(type: "integer")]
    protected int $amount;
}
