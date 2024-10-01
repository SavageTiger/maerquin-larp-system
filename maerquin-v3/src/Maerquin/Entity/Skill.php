<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
class Skill
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private string $id;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(type: "integer", nullable: false)]
    private int $points;

    #[ORM\Column(type: "integer", nullable: false)]
    private int $numberOfTimes;

    #[ORM\Column(type: "text", nullable: false, options: ['default' => ''])]
    private string $description = '';

    #[ORM\Column(type: "text", nullable: false, options: ['default' => ''])]
    private string $remarks = '';

    #[ORM\Column(type: "string", length: 64, nullable: false, options: ['default' => ''])]
    private string $distance = '';

    #[ORM\Column(type: "string", length: 64, nullable: false, options: ['default' => ''])]
    private string $duration = '';

    #[ORM\Column(type: "integer", nullable: false)]
    private int $usageCosts;

    #[ORM\Column(type: "boolean", nullable: false)]
    private bool $nonFree;

    #[ORM\Column(type: "boolean", nullable: false)]
    private bool $hidden;

    #[ORM\Column(type: "boolean", nullable: false)]
    private bool $spell;

    #[ORM\Column(type: "boolean", nullable: false)]
    private bool $innatePossible;

    #[ORM\Column(type: "integer", nullable: false)]
    private int $level;

    #[ORM\Column(type: "boolean", nullable: false)]
    private bool $availableAsComponents;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $componentPage;

    #[ORM\Column(type: "text", nullable: false, options: ['default' => ''])]
    private string $scrollText = '';

    #[ORM\ManyToOne(targetEntity: Element::class)]
    #[ORM\JoinColumn(name: "element_id", referencedColumnName: "id", nullable: true)]
    private ?Element $element;

    #[ORM\ManyToOne(targetEntity: Deity::class)]
    #[ORM\JoinColumn(name: "deity_id", referencedColumnName: "id", nullable: true)]
    private ?Deity $deity;

    #[ORM\ManyToOne(targetEntity: SkillType::class)]
    #[ORM\JoinColumn(name: "skilltype_id", referencedColumnName: "id", nullable: true)]
    private ?SkillType $skillType;

}
