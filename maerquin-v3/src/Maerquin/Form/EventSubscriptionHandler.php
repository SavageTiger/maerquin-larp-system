<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Entity\Player;
use SvenHK\Maerquin\Exception\Form\FormSubmissionException;
use SvenHK\Maerquin\Exception\Request\MissingFormFieldException;
use SvenHK\Maerquin\Exception\Subscription\SubscriptionException;
use SvenHK\Maerquin\Model\SubscriptionRole;
use SvenHK\Maerquin\Repository\EventRepository;

readonly class EventSubscriptionHandler
{
    public function __construct(
        private EventRepository $eventRepository,
    ) {
    }

    /**
     * @throws MissingFormFieldException
     * @throws SubscriptionException
     * @throws FormSubmissionException
     */
    public function subscribe(Event $event, Player $player, Request $request): void
    {
        $formResolver = FormResolver::createFromRequest($request);

        $role = $formResolver->getValue('role', 'subscription');

        $answers = array_filter(
            $formResolver->getArray('answers', 'subscription'),
            static fn(mixed $value): bool => is_string($value),
        );

        $event->subscribe(
            $player,
            SubscriptionRole::tryFrom($role) ?? throw SubscriptionException::roleInvalid($role),
            $answers,
        );

        $this->eventRepository->save($event);
    }

    /**
     * @throws SubscriptionException
     */
    public function markAsPaid(Event $event, string $subscriptionId): void
    {
        $event->markSubscriptionAsPaid($subscriptionId);

        $this->eventRepository->save($event);
    }

    /**
     * @throws SubscriptionException
     */
    public function markAsUnpaid(Event $event, string $subscriptionId): void
    {
        $event->markSubscriptionAsUnpaid($subscriptionId);

        $this->eventRepository->save($event);
    }
}
