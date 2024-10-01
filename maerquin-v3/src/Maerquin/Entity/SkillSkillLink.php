<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: "skillskilllink")]
class SkillSkillLink
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Skill::class)]
    #[ORM\JoinColumn(name: "firstskill_id", referencedColumnName: "id", nullable: false)]
    private Skill $firstSkill;

    #[ORM\ManyToOne(targetEntity: Skill::class)]
    #[ORM\JoinColumn(name: "secondskill_id", referencedColumnName: "id", nullable: false)]
    private Skill $secondSkill;

    #[ORM\Column(type: "boolean", nullable: false)]
    private bool $prerequisite;

    #[ORM\Column(type: "boolean", nullable: false)]
    private bool $exclusive;
}
