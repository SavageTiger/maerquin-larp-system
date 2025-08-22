<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use SvenHK\Maerquin\Entity\Character;

class CharacterCollection
{
    /**
     * @var Character[]
     */
    private readonly array $characters;

    public function __construct(Character ...$characters)
    {
        $this->characters = $characters;
    }

    public function serialize(bool $compact): array
    {
        return array_map(
            fn(Character $character): array => $character->serialize($compact),
            $this->characters,
        );
    }

    /**
     * @return Character[]
     */
    public function getAll(): array
    {
        return $this->characters;
    }
}
