<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\Deity as DeityModel;
use SvenHK\Maerquin\Model\Element as ElementModel;
use SvenHK\Maerquin\Model\Skill as SkillModel;
use SvenHK\Maerquin\Model\SkillSkillLink as SkillSkillLinkModel;
use SvenHK\Maerquin\Model\SkillType as SkillTypeModel;
use SvenHK\Maerquin\Repository\SkillRepository;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill extends SkillModel
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface $id;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    protected string $name;

    #[ORM\Column(nullable: false)]
    protected float $points;

    #[ORM\Column(type: 'integer', nullable: false)]
    protected int $maximumAmountBuyable;

    #[ORM\Column(type: 'text', nullable: false, options: ['default' => ''])]
    protected string $description = '';

    #[ORM\Column(type: 'text', nullable: false, options: ['default' => ''])]
    protected string $remarks = '';

    #[ORM\Column(type: 'string', length: 64, nullable: false, options: ['default' => ''])]
    protected string $distance = '';

    #[ORM\Column(type: 'string', length: 64, nullable: false, options: ['default' => ''])]
    protected string $duration = '';

    #[ORM\Column(type: 'boolean', nullable: false)]
    protected bool $nonFree;

    #[ORM\Column(type: 'boolean', nullable: false)]
    protected bool $hidden;

    #[ORM\Column(type: 'integer', nullable: false)]
    protected int $level;

    #[ORM\ManyToOne(targetEntity: Element::class)]
    #[ORM\JoinColumn(name: 'element_id', referencedColumnName: 'id', nullable: true)]
    protected null | ElementModel $element;

    #[ORM\ManyToOne(targetEntity: Deity::class)]
    #[ORM\JoinColumn(name: 'deity_id', referencedColumnName: 'id', nullable: true)]
    protected null | DeityModel $deity;

    #[ORM\ManyToOne(targetEntity: SkillType::class)]
    #[ORM\JoinColumn(name: 'skilltype_id', referencedColumnName: 'id', nullable: false)]
    protected SkillTypeModel $skillType;

    #[ORM\OneToOne(
        targetEntity: SkillSkillLink::class,
        mappedBy: 'secondSkill',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    protected null | SkillSkillLinkModel $requiredSkillLink = null;
}
