<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Model\CustomFieldCollection;
use SvenHK\Maerquin\Model\DeitiesCollection;
use SvenHK\Maerquin\Model\EventCollection;
use SvenHK\Maerquin\Model\PlayerCollection;
use SvenHK\Maerquin\Model\RaceCollection;
use SvenHK\Maerquin\Repository\DeityRepository;
use SvenHK\Maerquin\Repository\EventRepository;
use SvenHK\Maerquin\Repository\RaceRepository;

readonly class CharacterViewContext
{
    public function __construct(
        private EventRepository $eventRepository,
        private RaceRepository $raceRepository,
        private DeityRepository $deityRepository,
    ) {
    }

    /**
     * @return array<string, mixed> the variables character.html.twig needs
     */
    public function forCharacter(Character $character, PlayerCollection $players, CustomFieldCollection $customFields): array
    {
        return [
            'character' => $character,
            'eventsForCharacter' => new EventCollection($this->eventRepository->findAllForCharacter($character->getId())),
            'races' => new RaceCollection($this->raceRepository->findAllSorted()),
            'deities' => new DeitiesCollection($this->deityRepository->findAll()),
            'players' => $players,
            'customFields' => $customFields,
        ];
    }
}
