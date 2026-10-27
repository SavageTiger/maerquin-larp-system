<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Entity\EventSubscription as EventSubscriptionEntity;
use SvenHK\Maerquin\Entity\FormSubmission as FormSubmissionEntity;
use SvenHK\Maerquin\Entity\Player;
use SvenHK\Maerquin\Model\Player as PlayerModel;

class EventSubscription
{
    protected UuidInterface $id;
    protected Event $event;
    protected Player $player;
    protected DateTimeInterface $subscribedAt;
    protected FormSubmissionEntity $formSubmission;

    private function __construct(
        Event $event,
        Player $player,
        FormSubmissionEntity $formSubmission,
    ) {
        $this->id = Uuid::uuid4();
        $this->event = $event;
        $this->player = $player;
        $this->subscribedAt = new DateTimeImmutable();
        $this->formSubmission = $formSubmission;
    }

    public static function create(Event $event, Player $player, FormSubmissionEntity $formSubmission): self
    {
        return new EventSubscriptionEntity($event, $player, $formSubmission);
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

    public function isFor(PlayerModel $player): bool
    {
        return $this->player->getId() === $player->getId();
    }
}
