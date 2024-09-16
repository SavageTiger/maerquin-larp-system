<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;

/**
 * Skill Entity
 */
#[ORM\Entity]
class Skill
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    public Uuid $id;

    #[ORM\Column(type: "string", length: 255)]
    public string $name;

    #[ORM\Column(type: "integer")]
    public int $points;

    #[ORM\Column(type: "integer")]
    public int $numberOfTimes;

    #[ORM\Column(type: "text")]
    public string $description;

    #[ORM\Column(type: "text", nullable: true)]
    public ?string $remarks;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    public ?string $distance;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    public ?string $duration;

    #[ORM\Column(type: "boolean")]
    public bool $nonFree;

    #[ORM\Column(type: "boolean")]
    public bool $hidden;

    #[ORM\Column(type: "boolean")]
    public bool $spell;

    #[ORM\Column(type: "boolean")]
    public bool $innatePossible;

    #[ORM\Column(type: "integer")]
    public int $level;

    #[ORM\Column(type: "boolean")]
    public bool $availableAsComponents;

    #[ORM\ManyToOne(targetEntity: Element::class)]
    #[ORM\JoinColumn(nullable: true)]
    public ?Element $element;

    #[ORM\ManyToOne(targetEntity: Deity::class)]
    #[ORM\JoinColumn(nullable: true)]
    public ?Deity $deity;
}
