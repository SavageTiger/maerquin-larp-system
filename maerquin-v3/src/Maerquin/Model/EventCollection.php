<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

class EventCollection
{
    /**
     * @var Event[]
     */
    private array $events;

    public function __construct(array $events)
    {
        $this->events = $events;
    }

    public function serialize(bool $compact): array
    {
        return array_map(
            fn(Event $event) => $event->serialize($compact),
            $this->events,
        );
    }
}
