<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Warning;

use SvenHK\Maerquin\Model\Character;
use SvenHK\Maerquin\Repository\CharacterRepository;

final readonly class CharacterWarningUpdater
{
    public function __construct(
        private CharacterRepository $characterRepository,
        private CharacterWarning $characterWarning,
    ) {
    }

    public function updateAndSave(Character $character): void
    {
        $character->updateWarnings($this->characterWarning->getWarnings($character));

        $this->characterRepository->save($character);
    }

    public function updateAndSaveAllByRace(string $raceId): void
    {
        foreach ($this->characterRepository->findAllByRace($raceId) as $character) {
            $this->updateAndSave($character);
        }
    }

    public function updateAndSaveAllCoupledToSkill(string $skillId): void
    {
        foreach ($this->characterRepository->findAllCoupledToSkill($skillId) as $character) {
            $this->updateAndSave($character);
        }
    }
}
