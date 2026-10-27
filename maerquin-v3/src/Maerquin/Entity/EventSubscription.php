<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\EventSubscription as EventSubscriptionModel;

#[ORM\Entity]
#[ORM\Table(name: 'event_subscription')]
#[ORM\UniqueConstraint(name: 'unique_event_player', columns: ['event_id', 'player_id'])]
class EventSubscription extends EventSubscriptionModel
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    protected UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: false)]
    protected Event $event;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name: 'player_id', referencedColumnName: 'id', nullable: false)]
    protected Player $player;

    #[ORM\Column(type: 'datetime_immutable')]
    protected DateTimeInterface $subscribedAt;

    #[ORM\OneToOne(targetEntity: FormSubmission::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(name: 'form_submission_id', referencedColumnName: 'id', nullable: false)]
    protected FormSubmission $formSubmission;
}
