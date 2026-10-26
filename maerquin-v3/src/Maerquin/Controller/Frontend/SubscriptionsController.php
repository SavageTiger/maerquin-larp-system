<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\Frontend;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Model\User;
use SvenHK\Maerquin\Repository\EventRepository;
use SvenHK\Maerquin\Session\Session;

class SubscriptionsController extends Action
{
    public function __construct(
        LoggerInterface $logger,
        private readonly EventRepository $eventRepository,
        private readonly Session $session,
    ) {
        parent::__construct($logger);
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
