<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Player as PlayerEntity;

class Player
{
    protected UuidInterface $id;
    protected string $name;
    protected string $email;
    protected Collection $characters;

    public static function create(UuidInterface $id): self
    {
        return new PlayerEntity($id);
    }

    public function updatePlayer(string $name, string $email): void
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function serialize(bool $compact)
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
        return (string)$this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
