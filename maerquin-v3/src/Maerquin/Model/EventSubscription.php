<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Character;
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
    protected null | Character $character;
    protected DateTimeInterface $subscribedAt;
    protected SubscriptionRole $role;
    protected FormSubmissionEntity $formSubmission;
    protected PaymentState $paymentState;
    protected SubscriptionStatus $status;

    private function __construct(
        Event $event,
        Player $player,
        SubscriptionRole $role,
        null | Character $character,
        FormSubmissionEntity $formSubmission,
    ) {
        $this->id = Uuid::uuid4();
        $this->event = $event;
        $this->player = $player;
        $this->subscribedAt = new DateTimeImmutable();
        $this->role = $role;
        $this->character = $character;
        $this->formSubmission = $formSubmission;
        $this->paymentState = PaymentState::PendingPayment;
        $this->status = SubscriptionStatus::Received;
    }

    public static function create(
        Event $event,
        Player $player,
        SubscriptionRole $role,
        null | Character $character,
        FormSubmissionEntity $formSubmission,
    ): self {
        return new EventSubscriptionEntity($event, $player, $role, $character, $formSubmission);
    }

    public function serialize(): array
    {
        return [
            'id' => $this->getId(),
            'playerId' => $this->player->getId(),
            'playerName' => $this->player->getName(),
            'subscribedAt' => $this->subscribedAt->format(DateTimeInterface::ISO8601),
            'role' => $this->role->value,
            'paymentState' => $this->paymentState->value,
            'status' => $this->status->value,
        ];
    }

    public function getId(): string
    {
        return (string)$this->id;
    }

    public function getRole(): SubscriptionRole
    {
        return $this->role;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getCharacter(): null | Character
    {
        return $this->character;
    }

    public function getSubscribedAt(): DateTimeInterface
    {
        return $this->subscribedAt;
    }

    public function getFormSubmission(): FormSubmissionEntity
    {
        return $this->formSubmission;
    }

    public function getStatus(): SubscriptionStatus
    {
        return $this->status;
    }

    public function isPaid(): bool
    {
        return $this->paymentState === PaymentState::Paid;
    }

    public function confirm(): void
    {
        $this->status = SubscriptionStatus::Confirmed;
    }

    public function placeOnWaitingList(): void
    {
        $this->status = SubscriptionStatus::WaitingList;
    }

    public function resetStatus(): void
    {
        $this->status = SubscriptionStatus::Received;
    }

    public function markAsPaid(): void
    {
        $this->paymentState = PaymentState::Paid;
    }

    public function markAsUnpaid(): void
    {
        $this->paymentState = PaymentState::PendingPayment;
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
