<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\Frontend;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use SvenHK\Maerquin\Controller\RedirectTo;
use SvenHK\Maerquin\Exception\Form\FormSubmissionException;
use SvenHK\Maerquin\Exception\Subscription\SubscriptionException;
use SvenHK\Maerquin\Form\EventSubscriptionHandler;
use SvenHK\Maerquin\Model\SubscriptionRole;
use SvenHK\Maerquin\Model\SubscriptionState;
use SvenHK\Maerquin\Model\User;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\EventRepository;
use SvenHK\Maerquin\Session\Session;
use SvenHK\Maerquin\Translation\Translator;

class SubscriptionsController extends Action
{
    use RedirectTo;

    public function __construct(
        LoggerInterface $logger,
        private readonly EventRepository $eventRepository,
        private readonly EventSubscriptionHandler $eventSubscriptionHandler,
        private readonly CharacterRepository $characterRepository,
        private readonly Session $session,
        private readonly Translator $translator,
    ) {
        parent::__construct($logger);
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $eventId = $this->request->getAttribute('eventId');

        if (is_string($eventId) && $this->request->getMethod() === 'POST') {
            return $this->subscribe($eventId);
        }

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

    private function subscribe(string $eventId): ResponseInterface
    {
        $user = $this->session->getUser();

        if ($user instanceof User === false) {
            throw new HttpUnauthorizedException($this->request);
        }

        $event = $this->eventRepository->findById($eventId) ?? throw new HttpNotFoundException($this->request);

        try {
            $this->eventSubscriptionHandler->subscribe($event, $user->getPlayer(), $this->request);
        } catch (SubscriptionException $exception) {
            throw new HttpBadRequestException($this->request, $this->translator->trans($exception->getTranslationKey()), $exception);
        } catch (FormSubmissionException $exception) {
            throw new HttpBadRequestException(
                $this->request,
                $this->translator->trans($exception->getTranslationKey(), $exception->getTranslationParameters()),
                $exception,
            );
        }

        return new Response();
    }

    private function renderSubscribeView(string $eventId): ResponseInterface
    {
        $user = $this->session->getUser();

        if ($user instanceof User === false) {
            return $this->redirectTo(sprintf('/login.html?redirect=/subscriptions/%s.html', $eventId));
        }

        $event = $this->eventRepository->findById($eventId) ?? throw new HttpNotFoundException($this->request);

        if (
            $event->getSubscriptionState() !== SubscriptionState::Open
            || $event->getForm() === null
            || $event->isSubscribed($user->getPlayer())
        ) {
            return $this->redirectTo('/subscriptions.html');
        }

        return Twig::fromRequest($this->request)->render(
            $this->response,
            'Frontend/event_subscribe.html.twig',
            [
                'event' => $event,
                'roles' => SubscriptionRole::cases(),
                'characters' => $this->characterRepository->forPlayer($user->getPlayer()->getId()),
            ],
        );
    }
}
