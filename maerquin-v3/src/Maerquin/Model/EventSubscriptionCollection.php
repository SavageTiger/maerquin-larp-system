<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

class EventSubscriptionCollection
{
    /**
     * @var EventSubscription[]
     */
    private array $subscriptions;

    public function __construct(EventSubscription ...$subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }

    public function serialize(): array
    {
        return array_map(
            static fn(EventSubscription $subscription): array => $subscription->serialize(),
            $this->subscriptions,
        );
    }
}
