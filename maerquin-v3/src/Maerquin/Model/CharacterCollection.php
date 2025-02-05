<?php

namespace SvenHK\Maerquin\Model;

class CharacterCollection
{
    /**
     * @var SkillType[]
     */
    private array $characters;

    public function __construct(array $characters)
    {
        $this->characters = $characters;
    }

    public function serialize() : array
    {
        return array_map(
            fn(SkillType $character) => $character->serialize(),
            $this->characters
        );
    }
}

