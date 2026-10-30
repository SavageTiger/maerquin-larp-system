<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\Frontend;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;
use SvenHK\Maerquin\Controller\RedirectTo;
use SvenHK\Maerquin\Model\SubscriptionRole;
use SvenHK\Maerquin\Model\SubscriptionState;
use SvenHK\Maerquin\Model\User;
use SvenHK\Maerquin\Repository\EventRepository;
use SvenHK\Maerquin\Session\Session;

class SubscriptionsController extends Action
{
    use RedirectTo;

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
        $eventId = $this->request->getAttribute('eventId');

        if (is_string($eventId)) {
            return $this->renderSubscribeView($eventId);
        }

        $user = $this->session->getUser();

        return Twig::fromRequest($this->request)->render(
            $this->response,
            'Frontend/event_subscriptions.html.twig',
            [
                'events' => $this->eventRepository->findAllSorted(),
                'player' => $user instanceof User ? $user->getPlayer() : null,
            ],
        );
    }

    private function renderSubscribeView(string $eventId): ResponseInterface
    {
        $user = $this->session->getUser();

        if ($user instanceof User === false) {
            return $this->redirectTo(sprintf('/login.html?redirect=/subscriptions/%s.html', $eventId));
        }

        $event = $this->eventRepository->findById($eventId) ?? throw new HttpNotFoundException($this->request);

        if ($event->getSubscriptionState() !== SubscriptionState::Open || $event->isSubscribed($user->getPlayer())) {
            return $this->redirectTo('/subscriptions.html');
        }

        return Twig::fromRequest($this->request)->render(
            $this->response,
            'Frontend/event_subscribe.html.twig',
            [
                'event' => $event,
                'roles' => SubscriptionRole::cases(),
            ],
        );
    }
}
