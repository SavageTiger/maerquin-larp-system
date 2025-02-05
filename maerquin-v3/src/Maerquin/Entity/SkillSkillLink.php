<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use SvenHK\Maerquin\Model\Skill as SkillModel;
use SvenHK\Maerquin\Model\SkillSkillLink as SkillSkillLinkModel;

#[ORM\Entity]
#[ORM\Table(name: "skillskilllink")]
class SkillSkillLink extends SkillSkillLinkModel
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected string $id;

    #[ORM\ManyToOne(targetEntity: Skill::class)]
    #[ORM\JoinColumn(name: "firstskill_id", referencedColumnName: "id", nullable: false)]
    protected SkillModel $firstSkill;

    #[ORM\ManyToOne(targetEntity: Skill::class)]
    #[ORM\JoinColumn(name: "secondskill_id", referencedColumnName: "id", nullable: false)]
    protected SkillModel $secondSkill;
}
