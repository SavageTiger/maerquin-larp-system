<?php

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Deity;

class SkillType
{
    protected UuidInterface $id;
    protected string $name;
    protected ?Deity $primaryDeity;
    protected ?Deity $secondaryDeity;

    /**
     * @var SkillLink[]
     */
    protected Collection $skills;

    public function serialize() : array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'primaryDeityId' => $this->getPrimaryDeityId(),
            'secondaryDeityId' => $this->getSecondaryDeityId(),
            'playerId' => $this->playerId()
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

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getPrimaryDeityId() : string
    {
        return $this->primaryDeity?->getId() ?? '';
    }

    public function getSecondaryDeityId()
    {
        return $this->secondaryDeity?->getId() ?? '';
    }

    public function playerId() : string
    {
        return $this->player->getId();
    }

    /**
     * @return SkillLink[]
     */
    public function getSkills() : array
    {
        return $this->skills->toArray();
    }

}
