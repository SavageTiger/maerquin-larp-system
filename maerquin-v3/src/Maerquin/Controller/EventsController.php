<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Form\EventFormHandler;
use SvenHK\Maerquin\Model\CharacterCollection;
use SvenHK\Maerquin\Model\EventCollection;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\EventRepository;
use SvenHK\Maerquin\Repository\FormRepository;

class EventsController extends Action
{
    public function __construct(
        private readonly EventFormHandler $eventFormHandler,
        private readonly EventRepository $eventRepository,
        private readonly CharacterRepository $characterRepository,
        private readonly FormRepository $formRepository,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $eventId = (string)($this->request->getAttribute('eventId') ?? '');

        if (str_contains($this->request->getUri()->getPath(), 'create.html') === true) {
            $eventId = Uuid::uuid4()->toString();
        }

        if ($this->request->getMethod() === 'POST' && Uuid::isValid($eventId)) {
            $this->eventFormHandler->handle(
                $this->getEvent(Uuid::fromString($eventId)),
                $this->request,
            );
        }

        return $eventId !== ''
            ? $this->renderEventEditView($view, Uuid::fromString($eventId))
            : $this->renderEventListView($view);
    }

    private function getEvent(UuidInterface $eventId): Event
    {
        $event = $this->eventRepository->findById($eventId->toString());

        if ($event === null) {
            return Event::create($eventId);
        }

        return $event;
    }

    private function renderEventEditView(Twig $view, UuidInterface $eventId): ResponseInterface
    {
        $event = $this->getEvent($eventId);

        return $view->render(
            $this->response,
            'event.html.twig',
            [
                'event' => $event,
                'characters' => new CharacterCollection(
                    ...$this->characterRepository->findByEvent($eventId->toString()),
                ),
                'paidPlayerCharacters' => new CharacterCollection(...$event->getPaidPlayerCharacters()),
                'forms' => $this->formRepository->findAllSorted(),
                'persisted' => str_contains($this->request->getUri()->getPath(), '/persisted/'),
            ],
        );
    }

    private function renderEventListView(Twig $view): ResponseInterface
    {
        return $view->render(
            $this->response,
            'events.html.twig',
            [
                'events' => new EventCollection(
                    $this->eventRepository->findAllSorted(),
                ),
            ],
        );
    }
}
