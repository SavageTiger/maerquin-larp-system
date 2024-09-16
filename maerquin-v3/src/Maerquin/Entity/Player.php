<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;

/**
 * Player Entity
 */
#[ORM\Entity]
class Player
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    public Uuid $id;

    #[ORM\Column(type: "string", length: 255)]
    public string $name;

    #[ORM\OneToMany(mappedBy: "player", targetEntity: Character::class)]
    public Collection $characters;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
    }
}
