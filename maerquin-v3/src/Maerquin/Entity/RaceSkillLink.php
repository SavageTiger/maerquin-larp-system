<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\SkillRaceConnection;

#[ORM\Entity]
#[ORM\Table(name: 'raceSkillLink')]
class RaceSkillLink extends SkillRaceConnection
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    protected UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Race::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected Race $race;

    #[ORM\ManyToOne(targetEntity: Skill::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected Skill $skill;

    #[ORM\Column(type: 'boolean', nullable: false)]
    protected bool $mandatory;

    #[ORM\Column(type: 'boolean', nullable: false)]
    protected bool $forbidden;

    #[ORM\Column(type: 'float', nullable: false)]
    protected float $points;
}
