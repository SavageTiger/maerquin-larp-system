<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

class SkillRaceConnectionCollection
{
    /**
     * @var SkillRaceConnection[]
     */
    private array $skillRaceConnetions;

    public function __construct(array $skillRaceConnection)
    {
        $this->skillRaceConnetions = $skillRaceConnection;
    }

    public function serialize(): array
    {
        return array_map(
            fn(SkillRaceConnection $skillRaceConnection) => $skillRaceConnection->serialize(),
            $this->skillRaceConnetions,
        );
    }
}
