<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Exception\Request\MissingFormFieldException;
use SvenHK\Maerquin\Exception\Subscription\SubscriptionException;
use SvenHK\Maerquin\Form\EventSubscriptionHandler;
use SvenHK\Maerquin\Model\SubscriptionRole;
use SvenHK\Maerquin\Repository\EventRepository;
use SvenHK\Maerquin\Translation\Translator;

class SubscriptionsController extends Action
{
    private const int LATEST_EVENTS = 5;

    public function __construct(
        LoggerInterface $logger,
        private readonly EventRepository $eventRepository,
        private readonly EventSubscriptionHandler $eventSubscriptionHandler,
        private readonly Translator $translator,
    ) {
        parent::__construct($logger);
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $eventId = $this->request->getAttribute('eventId');
        $subscriptionId = $this->request->getAttribute('subscriptionId');

        if (is_string($eventId) && is_string($subscriptionId)) {
            return $this->updateSubscription($this->getEvent($eventId), $subscriptionId);
        }

        if (is_string($eventId)) {
            return $this->renderEventView($this->getEvent($eventId));
        }

        return Twig::fromRequest($this->request)->render(
            $this->response,
            'subscriptions.html.twig',
            [
                'events' => $this->eventRepository->findLatest(self::LATEST_EVENTS),
                'roles' => SubscriptionRole::cases(),
            ],
        );
    }

    private function updateSubscription(Event $event, string $subscriptionId): ResponseInterface
    {
        try {
            $this->eventSubscriptionHandler->update($event, $subscriptionId, $this->request);
        } catch (SubscriptionException $exception) {
            throw new HttpBadRequestException($this->request, $this->translator->trans($exception->getTranslationKey()), $exception);
        } catch (MissingFormFieldException $exception) {
            throw new HttpBadRequestException($this->request, $exception->getMessage(), $exception);
        }

        return new Response();
    }

    private function renderEventView(Event $event): ResponseInterface
    {
        return Twig::fromRequest($this->request)->render(
            $this->response,
            'subscriptions_event.html.twig',
            [
                'event' => $event,
                'roles' => SubscriptionRole::cases(),
            ],
        );
    }

    private function getEvent(string $eventId): Event
    {
        return $this->eventRepository->findById($eventId) ?? throw new HttpNotFoundException($this->request);
    }
}
