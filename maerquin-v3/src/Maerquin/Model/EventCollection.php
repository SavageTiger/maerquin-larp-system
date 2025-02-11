<?php

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

    public function serialize() : array
    {
        return array_map(
            fn(Event $event) => $event->serialize(),
            $this->events
        );
    }
}

