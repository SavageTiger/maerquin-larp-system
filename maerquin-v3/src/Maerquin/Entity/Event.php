<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\Event as EventModel;
use SvenHK\Maerquin\Repository\EventRepository;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event extends EventModel
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface $id;

    #[ORM\Column(length: 255)]
    protected string $name;

    #[ORM\Column(type: 'integer')]
    protected int $points;

    #[ORM\Column(length: 255, nullable: true)]
    protected string $secondaryName;

    #[ORM\Column(type: 'datetime_immutable')]
    protected DateTimeInterface $startDate;

    #[ORM\Column(type: 'datetime_immutable')]
    protected DateTimeInterface $endDate;

    #[ORM\Column(type: 'text', nullable: true)]
    protected null | string $notes;

    #[ORM\OneToMany(
        targetEntity: CharacterEventLink::class,
        mappedBy: 'event',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    protected Collection $charactersPresent;

    public function __construct()
    {
        $this->charactersPresent = new ArrayCollection();
    }
}
