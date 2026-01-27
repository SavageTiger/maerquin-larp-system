<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\Frontend;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Repository\EventRepository;

class SubscriptionsController extends Action
{
    /**
     * @var EventRepository
     */
    private EntityRepository $eventRepository;

    public function __construct(
        LoggerInterface $logger,
        EntityManager $entityManager,
    ) {
        parent::__construct($logger);

        $this->eventRepository = $entityManager->getRepository(Event::class);
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        return $this->renderAvailableEvents();
    }

    private function renderAvailableEvents(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'Frontend/event_subscriptions.html.twig',
            [
                'events' => $this->eventRepository->findAllSorted(),
            ],
        );
    }
}
