<?php

namespace SvenHK\Maerquin\Model;

class CharacterCollection
{
    /**
     * @var Character[]
     */
    private array $characters;

    public function __construct(array $characters)
    {
        $this->characters = $characters;
    }

    public function serialize(): array
    {
        $serialized = [];

        foreach ($this->characters as $character) {
            $serialized[] = $character->serialize();
        }

        return $serialized;
    }
}

