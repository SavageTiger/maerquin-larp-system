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

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Character[]
     */
    public function getCharacters(): array
    {
        return $this->characters->toArray();
    }
}

