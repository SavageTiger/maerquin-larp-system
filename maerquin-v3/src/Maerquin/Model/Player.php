<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

class Player
{
    protected UuidInterface $id;
    protected string $name;
    protected Collection $characters;

    /**
     * @return SkillType[]
     */
    public function getCharacters(): array
    {
        return $this->characters->toArray();
    }

    public function serialize($compact)
    {
        $characters = [];

        /** @var Character $character */
        foreach ($this->characters->toArray() as $character) {
            $characters[] = $character->serialize($compact);
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
