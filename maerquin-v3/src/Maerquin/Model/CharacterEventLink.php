<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\CharacterEventLink as CharacterEventLinkEntity;
use SvenHK\Maerquin\Entity\Event;

class CharacterEventLink
{
    protected UuidInterface $id;
    protected Character $character;
    protected Event $event;
    protected int $points;

    private function __construct(
        Event $event,
        Character $character,
    ) {
        $this->id = Uuid::uuid4();
        $this->event = $event;
        $this->character = $character;
        $this->points = $event->getPoints();
    }

    public static function createEventAndCharacter(
        Event $event,
        Character $character,
    ): self {
        return new CharacterEventLinkEntity(
            $event,
            $character,
        );
    }
}
