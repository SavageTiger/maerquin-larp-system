<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: "raceSkillLink")]
class RaceSkillLink
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Race::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Race $race;

    #[ORM\ManyToOne(targetEntity: Skill::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Skill $skill;

    #[ORM\Column(type: "boolean", nullable: false)]
    private bool $mandatory;

    #[ORM\Column(type: "boolean", nullable: false)]
    private bool $forbidden;

    #[ORM\Column(type: "integer", nullable: false)]
    private int $points;
}
