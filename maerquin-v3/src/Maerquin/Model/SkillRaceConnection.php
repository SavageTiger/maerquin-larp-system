<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Entity\Race as RaceEntity;
use SvenHK\Maerquin\Entity\Skill as SkillEntity;

class SkillRaceConnection
{
    protected UuidInterface $id;
    protected RaceEntity $race;
    protected string $name;
    protected bool $mandatory;
    protected bool $forbidden;
    protected int $points;
    protected SkillEntity $skill;

    public static function createMandatory(Race $race, SkillEntity $skill) : self
    {
        $connection = new static();
        $connection->race = $race;
        $connection->skill = $skill;
        $connection->mandatory = true;
        $connection->forbidden = false;
        $connection->points = 0;

        return $connection;
    }

    public static function createForbidden(Race $race, SkillEntity $skill) : self
    {
        $connection = new static();
        $connection->race = $race;
        $connection->skill = $skill;
        $connection->mandatory = false;
        $connection->forbidden = true;
        $connection->points = 0;

        return $connection;
    }

    public static function createWithCustomPoints(Race $race, SkillEntity $skill, int $points) : self
    {
        $connection = new static();
        $connection->race = $race;
        $connection->skill = $skill;
        $connection->mandatory = false;
        $connection->forbidden = false;
        $connection->points = $points;

        return $connection;
    }

    public function serialize() : array
    {
        return [
            'id' => $this->getId(),
            'skillName' => $this->skill->getName(),
            'skillId' => $this->skill->getId(),
            'mandatory' => $this->isMandatory(),
            'forbidden' => $this->isForbidden(),
            'customPoints' => $this->getCustomPoints(),
        ];
    }

    public function getId() : string
    {
        return $this->id->toString();
    }

    public function isMandatory() : bool
    {
        return $this->mandatory;
    }

    public function isForbidden() : bool
    {
        return $this->forbidden;
    }

    public function getCustomPoints() : int
    {
        return $this->points;
    }
}
