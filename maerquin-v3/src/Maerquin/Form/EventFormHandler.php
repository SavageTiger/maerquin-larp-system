<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use DateMalformedStringException;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use LogicException;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\CharacterEventLink;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Exception\Event\EventDateUnparsableException;
use SvenHK\Maerquin\Exception\Persistence\EntityNotFoundException;
use SvenHK\Maerquin\Exception\Request\MissingFormFieldException;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\EventRepository;
use SvenHK\Maerquin\Repository\FormRepository;

class EventFormHandler
{
    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly EventRepository $eventRepository,
        private readonly FormRepository $formRepository,
    ) {
    }

    /**
     * @throws EventDateUnparsableException
     * @throws EntityNotFoundException
     * @throws MissingFormFieldException
     * @throws DateMalformedStringException
     */
    public function handle(Event $event, Request $request): void
    {
        $formResolver = FormResolver::createFromRequest($request);

        $name = $formResolver->getValue('name', 'event');
        $startDateYear = (int)$formResolver->getValue('startDateYear', 'event');
        $startDateMonth = (int)$formResolver->getValue('startDateMonth', 'event');
        $startDateDay = (int)$formResolver->getValue('startDateDay', 'event');
        $endDateYear = (int)$formResolver->getValue('endDateYear', 'event');
        $endDateMonth = (int)$formResolver->getValue('endDateMonth', 'event');
        $endDateDay = (int)$formResolver->getValue('endDateDay', 'event');
        $points = (int)$formResolver->getValue('points', 'event');
        $notes = $formResolver->getValue('notes', 'event');

        $playerSubscriptionCap = (int)$formResolver->getValue('playerSubscriptionCap', 'event', '0');
        $npcSubscriptionCap = (int)$formResolver->getValue('npcSubscriptionCap', 'event', '0');
        $formId = $formResolver->getValue('formId', 'event', '');

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

        // A new event has no subscription fields yet: it closes 24 hours before the start and does not open until configured
        $defaultSubscriptionMoment = $startDate->modify('-24 hours');

        $subscriptionsOpensAt = $this->resolveDateTime($formResolver, 'subscriptionsOpensAt', $defaultSubscriptionMoment);
        $subscriptionsClosesAt = $this->resolveDateTime($formResolver, 'subscriptionsClosesAt', $defaultSubscriptionMoment);

        if (trim($name) === '') {
            $name = '(Naamloos)';
        }

        $event->updateEvent(
            $name,
            $formResolver->getValue('secondaryName', 'event'),
            $points,
            $startDate,
            $endDate,
            $notes,
            $formResolver->getValue('description', 'event', ''),
            $this->createCharacterPresenceList($event, $characterIds),
            $subscriptionsOpensAt,
            $subscriptionsClosesAt,
            max(0, $playerSubscriptionCap),
            max(0, $npcSubscriptionCap),
            $formId === '' ? null : $this->formRepository->getById($formId),
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
     * Reads the `<prefix>Day`, `<prefix>Month`, `<prefix>Year` and `<prefix>Time` fields.
     *
     * @throws EventDateUnparsableException
     * @throws MissingFormFieldException
     * @throws DateMalformedStringException
     */
    private function resolveDateTime(FormResolver $formResolver, string $prefix, DateTimeImmutable $default): DateTimeImmutable
    {
        $day = (int)$formResolver->getValue($prefix . 'Day', 'event', $default->format('d'));
        $month = (int)$formResolver->getValue($prefix . 'Month', 'event', $default->format('m'));
        $year = (int)$formResolver->getValue($prefix . 'Year', 'event', $default->format('Y'));
        $time = $formResolver->getValue($prefix . 'Time', 'event', $default->format('H:i'));

        $this->guardDate($year, $month, $day);
        $this->guardTime($time);

        return new DateTimeImmutable(sprintf('%04d-%02d-%02d %s', $year, $month, $day, $time));
    }

    private function guardTime(string $time): void
    {
        if (preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time) !== 1) {
            throw new EventDateUnparsableException('Invalid time format');
        }
    }

    /**
     * @param array<int, string> $characterIds
     *
     * @throws EntityNotFoundException
     */
    private function createCharacterPresenceList(
        Event $event,
        array $characterIds,
    ): Collection {
        $charactersPresent = [];

        foreach ($characterIds as $characterId) {
            $charactersPresent[] = CharacterEventLink::createEventAndCharacter(
                $event,
                $this->characterRepository->getById($characterId),
            );
        }

        return new ArrayCollection($charactersPresent);
    }
}
