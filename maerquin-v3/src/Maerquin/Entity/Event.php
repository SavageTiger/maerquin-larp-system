<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\Event as EventModel;
use SvenHK\Maerquin\Repository\EventRepository;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event extends EventModel
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
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

    #[ORM\Column(
        type: 'datetime_immutable',
        nullable: true,
        options: ['default' => '1970-01-01 00:00:00'],
    )]
    protected DateTimeInterface $subscriptionsOpensAt;

    #[ORM\Column(
        type: 'integer',
        nullable: true,
        options: ['default' => 0],
    )]
    protected int $playerSubscriptionCap;

    #[ORM\OneToMany(
        targetEntity: CharacterEventLink::class,
        mappedBy: 'event',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    protected Collection $charactersPresent;

    #[ORM\OneToMany(
        targetEntity: EventSubscription::class,
        mappedBy: 'event',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    protected Collection $subscriptions;

    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
        $this->charactersPresent = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->name = '';
        $this->secondaryName = '';
        $this->points = 20;
        $this->startDate = new DateTimeImmutable('-1 day');
        $this->endDate = new DateTimeImmutable('now');
        $this->subscriptionsOpensAt = new DateTimeImmutable('+14 days');
        $this->playerSubscriptionCap = 0;
    }
}
