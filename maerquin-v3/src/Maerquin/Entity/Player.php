<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\Player as PlayerModel;
use SvenHK\Maerquin\Repository\PlayerRepository;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player extends PlayerModel
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    protected UuidInterface $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $name = '';

    #[ORM\Column(type: 'string', length: 255)]
    protected string $email = '';

    #[ORM\OneToMany(targetEntity: Character::class, mappedBy: 'player')]
    protected Collection $characters;

    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
        $this->characters = new ArrayCollection();
    }
}
