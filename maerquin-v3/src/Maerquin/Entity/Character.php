<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\Character as CharacterModel;
use SvenHK\Maerquin\Model\Player as PlayerModel;
use SvenHK\Maerquin\Model\Race as RaceModel;
use SvenHK\Maerquin\Repository\CharacterRepository;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
#[ORM\Table(name: '`Character`')]
class Character extends CharacterModel
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface $id;

    #[ORM\Column(type: "string", length: 255)]
    protected string $name;

    #[ORM\Column(type: "string", length: 255, nullable: false, options: ['default' => ''])]
    protected string $title;

    #[ORM\ManyToOne(targetEntity: Deity::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Deity $primaryDeity = null;

    #[ORM\ManyToOne(targetEntity: Deity::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Deity $secondaryDeity = null;

    #[ORM\Column(type: "string", length: 255, nullable: false, options: ['default' => ''])]
    protected string $occupation = '';

    #[ORM\Column(type: "string", length: 255, nullable: false, options: ['default' => ''])]
    protected string $guild;

    #[ORM\Column(type: "string", length: 255, nullable: false, options: ['default' => ''])]
    protected string $birthplace;

    #[ORM\ManyToOne(targetEntity: Player::class, inversedBy: "characters")]
    #[ORM\JoinColumn(nullable: true)]
    protected ?PlayerModel $player;

    #[ORM\Column(type: "text", nullable: false, options: ['default' => ''])]
    protected string $notes;

    #[ORM\ManyToOne(targetEntity: Race::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected RaceModel $race;

    #[ORM\Column(type: "boolean")]
    protected bool $deceased;

    #[ORM\OneToMany(mappedBy: "character", targetEntity: CharacterSkillLink::class)]
    protected Collection $skills;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
    }
}
