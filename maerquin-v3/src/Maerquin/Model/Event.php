<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Event as EventEntity;

class Event
{
    protected UuidInterface $id;
    protected string $name;
    protected string $secondaryName;
    protected DateTimeInterface $startDate;
    protected DateTimeInterface $endDate;
    protected int $points;
    protected null | string $notes;
    protected Collection $charactersPresent;

    public static function create(UuidInterface $eventId): EventEntity
    {
        return new EventEntity($eventId);
    }

    public function serialize(bool $compact): array
    {
        if ($compact === true) {
            return [
                'id' => $this->getId(),
                'name' => $this->getName(),
                'points' => $this->getPoints(),
            ];
        }

        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'alternativeName' => $this->getSecondaryName(),
            'startDate' => $this->getStartDate()->format(DateTimeInterface::ISO8601),
            'endDate' => $this->getEndDate()->format(DateTimeInterface::ISO8601),
            'points' => $this->getPoints(),
            'notes' => $this->getNotes(),
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

    public function getPoints(): int
    {
        return $this->points;
    }

    public function getSecondaryName(): string
    {
        return $this->secondaryName;
    }

    public function getStartDate(): DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeInterface
    {
        return $this->endDate;
    }

    public function getNotes(): string
    {
        return $this->notes ?? '';
    }

    public function updateEvent(
        string $name,
        string $secondaryName,
        int $points,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        string $notes,
        Collection $charactersPresent,
    ): void {
        $this->name = $name;
        $this->secondaryName = $secondaryName;
        $this->points = $points;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->notes = $notes;
        $this->charactersPresent = $charactersPresent;
    }
}
