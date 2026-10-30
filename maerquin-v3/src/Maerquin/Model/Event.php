<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Character as CharacterEntity;
use SvenHK\Maerquin\Entity\Event as EventEntity;
use SvenHK\Maerquin\Entity\Form as FormEntity;
use SvenHK\Maerquin\Entity\Player as PlayerEntity;
use SvenHK\Maerquin\Exception\Form\FormSubmissionException;
use SvenHK\Maerquin\Exception\Subscription\SubscriptionException;

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

    public function getSubscriptionCap(SubscriptionRole $role): int
    {
        return match ($role) {
            SubscriptionRole::Player => $this->playerSubscriptionCap,
            SubscriptionRole::Npc => $this->npcSubscriptionCap,
        };
    }

    /**
     * @return list<EventSubscription> in order of subscription
     */
    public function getSubscriptions(SubscriptionRole $role): array
    {
        $subscriptions = array_values($this->subscriptions->filter(
            static fn(EventSubscription $subscription): bool => $subscription->hasRole($role),
        )->toArray());

        usort(
            $subscriptions,
            static fn(EventSubscription $a, EventSubscription $b): int => $a->getSubscribedAt() <=> $b->getSubscribedAt(),
        );

        return $subscriptions;
    }

    public function getOccupancy(SubscriptionRole $role): SubscriptionOccupancy
    {
        return SubscriptionOccupancy::fromSubscriptions(
            $this->getSubscriptionCap($role),
            $this->getSubscriptions($role),
        );
    }

    /**
     * @param array<string, string> $answers field key => value
     *
     * @throws SubscriptionException
     * @throws FormSubmissionException
     */
    public function subscribe(Player $player, SubscriptionRole $role, null | CharacterEntity $character, array $answers): void
    {
        assert($this instanceof EventEntity);
        assert($player instanceof PlayerEntity);

        if ($this->getSubscriptionState() !== SubscriptionState::Open) {
            throw SubscriptionException::notOpen();
        }

        if ($this->form === null) {
            throw SubscriptionException::formMissing();
        }

        if ($this->isSubscribed($player)) {
            throw SubscriptionException::alreadySubscribed();
        }

        if ($character !== null && ($role !== SubscriptionRole::Player || $character->isOwnedBy($player) === false)) {
            throw SubscriptionException::characterInvalid();
        }

        $this->subscriptions->add(EventSubscription::create(
            $this,
            $player,
            $role,
            $character,
            $this->form->createSubmission($answers),
        ));
    }

    public function findSubscription(Player $player): null | EventSubscription
    {
        return $this->subscriptions->findFirst(
            static fn(int $key, EventSubscription $subscription): bool => $subscription->isFor($player),
        );
    }

    /**
     * @throws SubscriptionException
     */
    public function markSubscriptionAsPaid(string $subscriptionId): void
    {
        $this->getSubscription($subscriptionId)->markAsPaid();
    }

    /**
     * @throws SubscriptionException
     */
    public function markSubscriptionAsUnpaid(string $subscriptionId): void
    {
        $this->getSubscription($subscriptionId)->markAsUnpaid();
    }

    /**
     * @throws SubscriptionException
     */
    public function confirmSubscription(string $subscriptionId): void
    {
        $this->getSubscription($subscriptionId)->confirm();
    }

    /**
     * @throws SubscriptionException
     */
    public function placeSubscriptionOnWaitingList(string $subscriptionId): void
    {
        $this->getSubscription($subscriptionId)->placeOnWaitingList();
    }

    /**
     * @throws SubscriptionException
     */
    public function resetSubscriptionStatus(string $subscriptionId): void
    {
        $this->getSubscription($subscriptionId)->resetStatus();
    }

    /**
     * @return list<CharacterEntity>
     */
    public function getPaidPlayerCharacters(): array
    {
        $characters = [];

        foreach ($this->getSubscriptions(SubscriptionRole::Player) as $subscription) {
            $character = $subscription->getCharacter();

            if ($subscription->isPaid() && $character !== null) {
                $characters[] = $character;
            }
        }

        return $characters;
    }

    public function isPresent(CharacterEntity $character): bool
    {
        return $this->charactersPresent->exists(
            static fn(int $key, CharacterEventLink $link): bool => $link->isFor($character),
        );
    }

    public function isSubscribed(Player $player): bool
    {
        return $this->findSubscription($player) !== null;
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

    /**
     * @throws SubscriptionException
     */
    private function getSubscription(string $subscriptionId): EventSubscription
    {
        return $this->subscriptions->findFirst(
            static fn(int $key, EventSubscription $subscription): bool => $subscription->getId() === $subscriptionId,
        ) ?? throw SubscriptionException::notFound();
    }
}
