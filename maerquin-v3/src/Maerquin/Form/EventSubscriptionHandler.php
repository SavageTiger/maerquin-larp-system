<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Entity\Player;
use SvenHK\Maerquin\Exception\Form\FormSubmissionException;
use SvenHK\Maerquin\Exception\Request\MissingFormFieldException;
use SvenHK\Maerquin\Exception\Subscription\SubscriptionException;
use SvenHK\Maerquin\Model\SubscriptionAction;
use SvenHK\Maerquin\Model\SubscriptionRole;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\EventRepository;

readonly class EventSubscriptionHandler
{
    public function __construct(
        private EventRepository $eventRepository,
        private CharacterRepository $characterRepository,
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
        $characterId = $formResolver->getValue('character', 'subscription', '');

        $answers = array_filter(
            $formResolver->getArray('answers', 'subscription'),
            static fn(mixed $value): bool => is_string($value),
        );

        $event->subscribe(
            $player,
            SubscriptionRole::tryFrom($role) ?? throw SubscriptionException::roleInvalid($role),
            $characterId === ''
                ? null
                : $this->characterRepository->findById($characterId) ?? throw SubscriptionException::characterInvalid(),
            $answers,
        );

        $this->eventRepository->save($event);
    }

    /**
     * @throws MissingFormFieldException
     * @throws SubscriptionException
     */
    public function update(Event $event, string $subscriptionId, Request $request): void
    {
        $action = FormResolver::createFromRequest($request)->getValue('action', 'subscription');

        switch (SubscriptionAction::tryFrom($action) ?? throw SubscriptionException::actionInvalid($action)) {
            case SubscriptionAction::Confirm:
                $event->confirmSubscription($subscriptionId);
                break;
            case SubscriptionAction::PlaceOnWaitingList:
                $event->placeSubscriptionOnWaitingList($subscriptionId);
                break;
            case SubscriptionAction::ResetStatus:
                $event->resetSubscriptionStatus($subscriptionId);
                break;
            case SubscriptionAction::MarkAsPaid:
                $event->markSubscriptionAsPaid($subscriptionId);
                break;
            case SubscriptionAction::MarkAsUnpaid:
                $event->markSubscriptionAsUnpaid($subscriptionId);
                break;
        }

        $this->eventRepository->save($event);
    }

}
