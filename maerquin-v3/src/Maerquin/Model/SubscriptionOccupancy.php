<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

final readonly class SubscriptionOccupancy
{
    private function __construct(
        public int $cap,
        public int $registered,
        public int $confirmed,
        public int $waitingList,
    ) {
    }

    /**
     * @param list<EventSubscription> $subscriptions all subscriptions for a single role
     */
    public static function fromSubscriptions(int $cap, array $subscriptions): self
    {
        $countWithStatus = static fn(SubscriptionStatus $status): int => count(array_filter(
            $subscriptions,
            static fn(EventSubscription $subscription): bool => $subscription->getStatus() === $status,
        ));

        return new self(
            cap: $cap,
            registered: count($subscriptions),
            confirmed: $countWithStatus(SubscriptionStatus::Confirmed),
            waitingList: $countWithStatus(SubscriptionStatus::WaitingList),
        );
    }

    public function hasCap(): bool
    {
        return $this->cap > 0;
    }

    public function isFull(): bool
    {
        return $this->hasCap() && $this->registered >= $this->cap;
    }

    public function isOverbooked(): bool
    {
        return $this->hasCap() && $this->confirmed > $this->cap;
    }

    public function getPlacesLeft(): int
    {
        return max(0, $this->cap - $this->registered);
    }
}
