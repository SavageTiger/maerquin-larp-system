<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Entity\EventSubscription as EventSubscriptionEntity;
use SvenHK\Maerquin\Entity\Player;

class EventSubscription
{
    protected UuidInterface $id;
    protected Event $event;
    protected Player $player;
    protected DateTimeInterface $subscribedAt;

    private function __construct(
        Event $event,
        Player $player,
    ) {
        $this->id = Uuid::uuid4();
        $this->event = $event;
        $this->player = $player;
        $this->subscribedAt = new DateTimeImmutable();
    }

    public static function create(Event $event, Player $player): self
    {
        return new EventSubscriptionEntity($event, $player);
    }

    public function serialize(): array
    {
        return [
            'id' => $this->getId(),
            'playerId' => $this->player->getId(),
            'playerName' => $this->player->getName(),
            'subscribedAt' => $this->subscribedAt->format(DateTimeInterface::ISO8601),
        ];
    }

    public function getId(): string
    {
        return (string)$this->id;
    }
}
