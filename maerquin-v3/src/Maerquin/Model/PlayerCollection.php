<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

class PlayerCollection
{
    /**
     * @var Player[]
     */
    private array $players;

    public function __construct(array $players)
    {
        $this->players = $players;
    }

    public function serialize(bool $compact): array
    {
        $serialized = [];

        foreach ($this->players as $player) {
            $serialized[] = $player->serialize($compact);
        }

        return $serialized;
    }
}
