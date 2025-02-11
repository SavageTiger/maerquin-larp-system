<?php

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Model\EventCollection;
use SvenHK\Maerquin\Repository\EventRepository;

class EventsController extends Action
{
    /**
     * @var EventRepository
     */
    private EntityRepository $eventReposotiry;

    public function __construct(EntityManager $entityManager)
    {
        $this->eventReposotiry = $entityManager->getRepository(Event::class);
    }

    public function action() : ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'events.html.twig',
            [
                'events' => new EventCollection($this->eventReposotiry->findAllSorted())
            ]
        );
    }
}
