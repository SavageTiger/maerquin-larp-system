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
use SvenHK\Maerquin\Model\User;
use SvenHK\Maerquin\Repository\EventRepository;
use SvenHK\Maerquin\Session\Session;

class SubscriptionsController extends Action
{
    /**
     * @var EventRepository
     */
    private EntityRepository $eventRepository;

    public function __construct(
        LoggerInterface $logger,
        EntityManager $entityManager,
        private readonly Session $session,
    ) {
        parent::__construct($logger);

        $this->eventRepository = $entityManager->getRepository(Event::class);
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);
        $user = $this->session->getUser();

        return $view->render(
            $this->response,
            'Frontend/event_subscriptions.html.twig',
            [
                'events' => $this->eventRepository->findAllSorted(),
                'player' => $user instanceof User ? $user->getPlayer() : null,
            ],
        );
    }
}
