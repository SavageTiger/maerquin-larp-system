<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\Race as RaceModel;
use SvenHK\Maerquin\Repository\RaceRepository;

#[ORM\Entity(repositoryClass: RaceRepository::class)]
class Race extends RaceModel
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface $id;

    #[ORM\Column(type: "string", length: 255)]
    protected string $name;

    #[ORM\OneToMany(
        targetEntity: RaceSkillLink::class,
        mappedBy: "race",
        orphanRemoval: true,
        cascade: ["PERSIST", "REMOVE"])
    ]
    protected Collection $skillConnections;
}
