<?php

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Character;

class Player
{
    protected UuidInterface $id;
    protected string $name;
    protected Collection $characters;

    /**
     * @return Character[]
     */
    public function getCharacters(): array
    {
        return $this->characters->toArray();
    }

    public function serialize()
    {
        $characters = [];

        /** @var Character $character */
        foreach ($this->characters->toArray() as $character) {
            $characters[] = $character->serialize();
        }

        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'characters' => $characters,
        ];

    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

