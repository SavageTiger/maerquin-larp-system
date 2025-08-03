<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use SvenHK\Maerquin\Entity\Character;

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

    public function serialize($compact): array
    {
        return array_map(
            fn(Character $character) => $character->serialize($compact),
            $this->characters,
        );
    }
}
