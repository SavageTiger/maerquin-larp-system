<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\Race as RaceModel;
use SvenHK\Maerquin\Repository\RaceRepository;

#[ORM\Entity(repositoryClass: RaceRepository::class)]
class Race extends RaceModel
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    protected UuidInterface $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $name;

    #[ORM\OneToMany(
        targetEntity: RaceSkillLink::class,
        mappedBy: 'race',
        cascade: ['PERSIST', 'REMOVE'],
        orphanRemoval: true,
    )]
    protected Collection $skillConnections;

    public function __construct()
    {
        $this->name = '';
        $this->skillConnections = new ArrayCollection();
    }
}
