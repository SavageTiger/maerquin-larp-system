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
    protected SubscriptionRole $role;
    protected FormSubmissionEntity $formSubmission;

    private function __construct(
        Event $event,
        Player $player,
        SubscriptionRole $role,
        FormSubmissionEntity $formSubmission,
    ) {
        $this->id = Uuid::uuid4();
        $this->event = $event;
        $this->player = $player;
        $this->subscribedAt = new DateTimeImmutable();
        $this->role = $role;
        $this->formSubmission = $formSubmission;
    }

    public static function create(
        Event $event,
        Player $player,
        SubscriptionRole $role,
        FormSubmissionEntity $formSubmission,
    ): self {
        return new EventSubscriptionEntity($event, $player, $role, $formSubmission);
    }

    public function serialize(): array
    {
        return [
            'id' => $this->getId(),
            'playerId' => $this->player->getId(),
            'playerName' => $this->player->getName(),
            'subscribedAt' => $this->subscribedAt->format(DateTimeInterface::ISO8601),
            'role' => $this->role->value,
        ];
    }

    public function getId(): string
    {
        return (string)$this->id;
    }

    public function hasRole(SubscriptionRole $role): bool
    {
        return $this->role === $role;
    }

    public function isFor(PlayerModel $player): bool
    {
        return $this->player->getId() === $player->getId();
    }
}
