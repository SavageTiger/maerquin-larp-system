<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;

/**
 * CharacterSkillLink Entity
 */
#[ORM\Entity]
class CharacterSkillLink
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    public Uuid $id;

    #[ORM\ManyToOne(targetEntity: Character::class, inversedBy: "skills")]
    #[ORM\JoinColumn(nullable: false)]
    public Character $character;

    #[ORM\ManyToOne(targetEntity: Skill::class)]
    #[ORM\JoinColumn(nullable: false)]
    public Skill $skill;

    #[ORM\Column(type: "integer")]
    public int $points;

    #[ORM\Column(type: "boolean")]
    public bool $teaching;

    #[ORM\Column(type: "boolean")]
    public bool $innate;

    #[ORM\Column(type: "integer")]
    public int $amount;
}
