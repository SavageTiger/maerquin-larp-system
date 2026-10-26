<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Model\EventCollection;
use SvenHK\Maerquin\Repository\EventRepository;

class SubscriptionsController extends Action
{
    public function __construct(
        LoggerInterface $logger,
        private readonly EventRepository $eventRepository,
    ) {
        parent::__construct($logger);
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        return $this->renderSubscriptionListView();
    }

    private function renderSubscriptionListView(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'subscriptions.html.twig',
            [
                'events' => new EventCollection($this->eventRepository->findAll()),
            ],
        );
    }
}
