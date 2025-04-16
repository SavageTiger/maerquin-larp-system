<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

class Event
{
    protected UuidInterface $id;
    protected string $name;
    protected string $secondaryName;
    protected DateTimeInterface $startDate;
    protected DateTimeInterface $endDate;
    protected int $points;
    protected Collection $charactersPresent;

    public function serialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'alternativeName' => $this->getSecondaryName(),
            'startDate' => $this->getStartDate()->format(DateTimeInterface::ISO8601),
            'endDate' => $this->getEndDate()->format(DateTimeInterface::ISO8601),
            'points' => $this->getPoints(),
        ];
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getSecondaryName() : string
    {
        return $this->secondaryName;
    }

    public function getStartDate() : DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate() : DateTimeInterface
    {
        return $this->endDate;
    }

    public function getPoints() : int
    {
        return $this->points;
    }

    public function updateEvent(
        string $name,
        string $secondaryName,
        int $points,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
    ) : void {
        $this->name = $name;
        $this->secondaryName = $secondaryName;
        $this->points = $points;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
}
