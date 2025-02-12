<?php

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Form\EventFormHandler;
use SvenHK\Maerquin\Model\CharacterCollection;
use SvenHK\Maerquin\Model\EventCollection;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\EventRepository;

class EventsController extends Action
{
    /**
     * @var EventRepository
     */
    private EntityRepository $eventRepository;

    /**
     * @var CharacterRepository
     */
    private EntityRepository $characterRepository;

    public function __construct(
        readonly private EventFormHandler $eventFormHandler,
        EntityManager $entityManager
    ) {
        $this->eventRepository = $entityManager->getRepository(Event::class);
        $this->characterRepository = $entityManager->getRepository(Character::class);
    }

    public function action() : ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $eventId = (string)($this->request->getAttribute('eventId') ?? '');

        if ($this->request->getMethod() === 'POST' && Uuid::isValid($eventId)) {
            $this->eventFormHandler->handle($eventId, $this->request);
        }

        if ($eventId !== '') {
            return $view->render(
                $this->response,
                'event.html.twig',
                [
                    'event' => $this->eventRepository->getById($eventId),
                    'characters' => new CharacterCollection($this->characterRepository->findByEvent($eventId)),
                ]
            );

        }

        return $view->render(
            $this->response,
            'events.html.twig',
            [
                'events' => new EventCollection($this->eventRepository->findAllSorted())
            ]
        );
    }
}
