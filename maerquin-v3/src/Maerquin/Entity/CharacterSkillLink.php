<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\SkillLink;

#[ORM\Entity]
class CharacterSkillLink extends SkillLink
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Character::class, inversedBy: 'skills')]
    #[ORM\JoinColumn(nullable: false)]
    protected Character $character;

    #[ORM\ManyToOne(targetEntity: Skill::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected Skill $skill;

    #[ORM\Column]
    protected float $points;

    #[ORM\Column(type: 'integer')]
    protected int $amount;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
    protected bool $armouredCasting = false;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
    protected bool $fastCasting = false;
}
