<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\Deity as DeityModel;
use SvenHK\Maerquin\Model\Element as ElementModel;
use SvenHK\Maerquin\Model\Skill as SkillModel;
use SvenHK\Maerquin\Model\SkillType as SkillTypeModel;
use SvenHK\Maerquin\Repository\SkillRepository;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill extends SkillModel
{
    #[ORM\Id]
    #[Column(type: 'uuid', unique: true)]
    protected UuidInterface $id;

    #[Column(type: 'string', length: 255, nullable: false)]
    protected string $name;

    #[Column(nullable: false)]
    protected float $points;

    #[Column(type: 'integer', nullable: false)]
    protected int $maximumAmountBuyable;

    #[Column(type: 'text', nullable: false, options: ['default' => ''])]
    protected string $description;

    #[Column(type: 'text', nullable: false, options: ['default' => ''])]
    protected string $remarks;

    #[Column(type: 'string', length: 64, nullable: false, options: ['default' => ''])]
    protected string $distance;

    #[Column(type: 'string', length: 64, nullable: false, options: ['default' => ''])]
    protected string $duration;

    #[Column(type: 'boolean', nullable: false)]
    protected bool $nonFree;

    #[Column(type: 'boolean', nullable: false)]
    protected bool $hidden;

    #[Column(type: 'integer', nullable: false)]
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

    #[ORM\OneToMany(
        targetEntity: SkillSkillLink::class,
        mappedBy: 'secondSkill',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    protected null | Collection $requiredSkills = null;

    #[Column(options: ['default' => false])]
    protected bool $canFastCast;

    #[Column(options: ['default' => false])]
    protected bool $canArmorCast;

    public function __construct()
    {
        $this->name = '';
        $this->deity = null;
        $this->element = null;
        $this->maximumAmountBuyable = 1;
        $this->points = 0;
        $this->level = 0;
        $this->hidden = false;
        $this->nonFree = false;
        $this->canArmorCast = false;
        $this->canFastCast = false;
        $this->description = '';
        $this->distance = '';
        $this->duration = '';
        $this->remarks = '';
    }
}
