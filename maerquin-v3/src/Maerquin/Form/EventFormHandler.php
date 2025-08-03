<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use DateMalformedStringException;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use LogicException;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\CharacterEventLink;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Exception\Event\EventDateUnparsableException;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\EventRepository;

class EventFormHandler
{
    /**
     * @var EventRepository
     */
    private EntityRepository $eventRepository;

    /**
     * @var CharacterRepository
     */
    private EntityRepository $characterRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->characterRepository = $entityManager->getRepository(Character::class);
        $this->eventRepository = $entityManager->getRepository(Event::class);
    }

    /**
     * @throws EventDateUnparsableException
     * @throws MaerquinEntityNotFoundException
     * @throws MissingFormFieldException
     * @throws DateMalformedStringException
     */
    public function handle(string $eventId, Request $request): void
    {
        $formResolver = FormResolver::createFromRequest($request);

        $startDateYear = (int)$formResolver->getValue('startDateYear', 'event');
        $startDateMonth = (int)$formResolver->getValue('startDateMonth', 'event');
        $startDateDay = (int)$formResolver->getValue('startDateDay', 'event');
        $endDateYear = (int)$formResolver->getValue('endDateYear', 'event');
        $endDateMonth = (int)$formResolver->getValue('endDateMonth', 'event');
        $endDateDay = (int)$formResolver->getValue('endDateDay', 'event');
        $points = (int)$formResolver->getValue('points', 'event');
        $notes = $formResolver->getValue('notes', 'event');

        $characterIds = json_decode($formResolver->getValue('characters', 'event'), true);

        if (is_array($characterIds) === false) {
            throw new LogicException('characters field is not valid JSON');
        }

        $this->guardDate($startDateYear, $startDateMonth, $startDateDay);
        $this->guardDate($endDateYear, $endDateMonth, $endDateDay);

        $startDate = new DateTimeImmutable(
            sprintf('%04d-%02d-%02d', $startDateYear, $startDateMonth, $startDateDay),
        );

        $endDate = new DateTimeImmutable(
            sprintf('%04d-%02d-%02d', $endDateYear, $endDateMonth, $endDateDay),
        );

        $event = $this->eventRepository->getById($eventId);

        $event->updateEvent(
            $formResolver->getValue('name', 'event'),
            $formResolver->getValue('secondaryName', 'event'),
            $points,
            $startDate,
            $endDate,
            $notes,
            $this->createCharacterPresenceList($event, $points, $characterIds),
        );

        $this->eventRepository->save($event);
    }

    private function guardDate(int $year, int $month, int $day): void
    {
        if (
            $year < 1_950 ||
            $year > 2_100 ||
            $month < 1 ||
            $month > 12 ||
            $day < 1 ||
            $day > 31
        ) {
            throw new EventDateUnparsableException('Invalid date format');
        }
    }

    /**
     * @param array<int, string> $characterIds
     *
     * @throws MaerquinEntityNotFoundException
     */
    private function createCharacterPresenceList(
        Event $event,
        int $points,
        array $characterIds,
    ): Collection {
        $charactersPresent = [];

        foreach ($characterIds as $characterId) {
            $charactersPresent[] = CharacterEventLink::createEventAndCharacter(
                $event,
                $points,
                $this->characterRepository->getById($characterId),
            );
        }

        return new ArrayCollection($charactersPresent);
    }
}
