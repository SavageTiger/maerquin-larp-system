<?php

namespace SvenHK\Maerquin\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
class Event
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    public Uuid $id;

    #[ORM\Column(length: 255)]
    public string $name;

    #[ORM\Column(type: 'integer')]
    public int $points;

    #[ORM\Column(length: 255, nullable: true)]
    public string $secondaryName;

    #[ORM\Column(type: 'datetime')]
    public DateTimeInterface $startDate;

    #[ORM\Column(type: 'datetime')]
    public DateTimeInterface $endDate;

    #[ORM\Column(type: 'text', nullable: true)]
    public string $notes;
}
