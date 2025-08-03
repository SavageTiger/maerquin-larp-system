<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

class Race
{
    protected UuidInterface $id;
    protected string $name;
    protected Collection $skillConnections;

    public function updateRace(string $name, array $skillConnections): void
    {
        $this->name = $name;

        $this->skillConnections->clear();

        foreach ($skillConnections as $skillConnection) {
            $this->skillConnections->add($skillConnection);
        }
    }

    public function serialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
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
}
