<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Event as EventEntity;
use SvenHK\Maerquin\Entity\Form as FormEntity;

class Event
{
    protected UuidInterface $id;
    protected string $name;
    protected string $secondaryName;
    protected DateTimeInterface $startDate;
    protected DateTimeInterface $endDate;
    protected int $points;
    protected null | string $notes;

    protected string $description;
    protected DateTimeInterface $subscriptionsOpensAt;
    protected int $playerSubscriptionCap;
    protected int $npcSubscriptionCap;
    protected Collection $charactersPresent;
    protected Collection $subscriptions;
    protected null | FormEntity $form;

    public static function create(UuidInterface $eventId): EventEntity
    {
        return new EventEntity($eventId);
    }

    public function serialize(bool $compact): array
    {
        if ($compact === true) {
            return [
                'id' => $this->getId(),
                'name' => $this->getName(),
                'points' => $this->getPoints(),
            ];
        }

        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'alternativeName' => $this->getSecondaryName(),
            'startDate' => $this->getStartDate()->format(DateTimeInterface::ISO8601),
            'endDate' => $this->getEndDate()->format(DateTimeInterface::ISO8601),
            'points' => $this->getPoints(),
            'notes' => $this->getNotes(),
            'description' => $this->getDescription(),
        ];
    }

    public function getId(): string
    {
        return (string)$this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function getSecondaryName(): string
    {
        return $this->secondaryName;
    }

    public function getStartDate(): DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeInterface
    {
        return $this->endDate;
    }

    public function getNotes(): string
    {
        return $this->notes ?? '';
    }

    public function getSubscriptionsOpensAt(): DateTimeInterface
    {
        return $this->subscriptionsOpensAt;
    }

    public function getPlayerSubscriptionCap(): int
    {
        return $this->playerSubscriptionCap;
    }

    public function getNpcSubscriptionCap(): int
    {
        return $this->npcSubscriptionCap;
    }

    public function getForm(): null | FormEntity
    {
        return $this->form;
    }

    public function isSubscribed(Player $player): bool
    {
        return $this->subscriptions->exists(
            static fn(int $key, EventSubscription $subscription): bool => $subscription->isFor($player),
        );
    }

    public function hasParticipated(Player $player): bool
    {
        return $this->charactersPresent->exists(
            static fn(int $key, CharacterEventLink $link): bool => $link->isPlayedBy($player),
        );
    }

    public function getSubscriptionState(null | DateTimeInterface $now = null): SubscriptionState
    {
        $now ??= new DateTimeImmutable();

        if ($now >= $this->startDate) {
            return SubscriptionState::Expired;
        }

        if ($now >= $this->subscriptionsOpensAt) {
            return SubscriptionState::Open;
        }

        return SubscriptionState::Pending;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function updateEvent(
        string $name,
        string $secondaryName,
        int $points,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        string $notes,
        string $description,
        Collection $charactersPresent,
        DateTimeImmutable $subscriptionsOpensAt,
        int $playerSubscriptionCap,
        int $npcSubscriptionCap,
        null | FormEntity $form,
    ): void {
        $this->form = $form;
        $this->name = $name;
        $this->secondaryName = $secondaryName;
        $this->points = $points;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->notes = $notes;
        $this->description = $description;
        $this->charactersPresent = $charactersPresent;
        $this->subscriptionsOpensAt = $subscriptionsOpensAt;
        $this->playerSubscriptionCap = $playerSubscriptionCap;
        $this->npcSubscriptionCap = $npcSubscriptionCap;
    }
}
