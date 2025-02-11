<?php

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Model\EventCollection;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\EventRepository;

class EventsController extends Action
{
    /**
     * @var EventRepository
     */
    private EntityRepository $eventReposotiry;

    /**
     * @var CharacterRepository
     */
    private EntityRepository $characterRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->eventReposotiry = $entityManager->getRepository(Event::class);
        $this->characterRepository = $entityManager->getRepository(Character::class);
    }

    public function action() : ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $eventId = $this->request->getAttribute('eventId');

        if ($eventId !== null) {
            return $view->render(
                $this->response,
                'event.html.twig',
                [
                    'event' => $this->eventReposotiry->getById($eventId),
                    'characters' => $this->characterRepository->findByEvent($eventId),
                ]
            );

        }

        return $view->render(
            $this->response,
            'events.html.twig',
            [
                'events' => new EventCollection($this->eventReposotiry->findAllSorted())
            ]
        );
    }
}
