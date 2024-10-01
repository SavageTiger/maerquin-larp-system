<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
class Character
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    public Uuid $id;

    #[ORM\Column(type: "string", length: 255)]
    public string $name;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    public ?string $title;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    public ?string $deity;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    public ?string $occupation;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    public ?string $guild;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    public ?string $birthplace;

    #[ORM\ManyToOne(targetEntity: Player::class, inversedBy: "characters")]
    #[ORM\JoinColumn(nullable: false)]
    public Player $player;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    public ?string $notes;

    #[ORM\ManyToOne(targetEntity: Archetype::class, inversedBy: "characters")]
    #[ORM\JoinColumn(nullable: true)]
    public ?Archetype $archetype;

    #[ORM\Column(type: "boolean")]
    public bool $inactive;

    #[ORM\ManyToOne(targetEntity: Race::class, inversedBy: "characters")]
    #[ORM\JoinColumn(nullable: true)]
    public ?Race $race;

    #[ORM\Column(type: "boolean")]
    public bool $deceased;

    #[ORM\OneToMany(mappedBy: "character", targetEntity: CharacterSkillLink::class)]
    public Collection $skills;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
    }
}
