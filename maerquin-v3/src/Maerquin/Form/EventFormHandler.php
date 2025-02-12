<?php

namespace SvenHK\Maerquin\Form;

use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Exception\Event\EventDateUnparsableException;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Repository\EventRepository;

class EventFormHandler
{
    /**
     * @var EventRepository
     */
    private EntityRepository $eventRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->eventRepository = $entityManager->getRepository(Event::class);
    }

    /**
     * @throws MissingFormFieldException
     * @throws MaerquinEntityNotFoundException
     */
    public function handle(string $eventId, Request $request)
    {
        $formResolver = FormResolver::createFromRequest($request);

        $startDateYear = (int)$formResolver->getValue('startDateYear', 'event');
        $startDateMonth = (int)$formResolver->getValue('startDateMonth', 'event');
        $startDateDay = (int)$formResolver->getValue('startDateDay', 'event');
        $endDateYear = (int)$formResolver->getValue('endDateYear', 'event');
        $endDateMonth = (int)$formResolver->getValue('endDateMonth', 'event');
        $endDateDay = (int)$formResolver->getValue('endDateDay', 'event');

        $this->guardDate($startDateYear, $startDateMonth, $startDateDay);
        $this->guardDate($endDateYear, $endDateMonth, $endDateDay);

        $startDate = new DateTimeImmutable(
            sprintf('%04d-%02d-%02d', $startDateYear, $startDateMonth, $startDateDay)
        );

        $endDate = new DateTimeImmutable(
            sprintf('%04d-%02d-%02d', $endDateYear, $endDateMonth, $endDateDay)
        );

        $event = $this->eventRepository->getById($eventId);

        $event->updateEvent(
            $formResolver->getValue('name', 'event'),
            $formResolver->getValue('secondaryName', 'event'),
            (int)$formResolver->getValue('points', 'event'),
            $startDate,
            $endDate
        // TODO: Characters with their points!.
        );

        $this->eventRepository->save($event);
    }

    private function guardDate(int $year, int $month, int $day) : void
    {
        if (
            $year < 1950 ||
            $year > 2100 ||
            $month < 1 ||
            $month > 12 ||
            $day < 1 ||
            $day > 31
        ) {
            throw new EventDateUnparsableException('Invalid date format');
        }
    }
}
