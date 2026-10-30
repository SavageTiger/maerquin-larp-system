<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpNotFoundException;
use SvenHK\Maerquin\Export\SubscriberListExport;
use SvenHK\Maerquin\Repository\EventRepository;

class SubscriptionsExportController extends Action
{
    public function __construct(
        LoggerInterface $logger,
        private readonly EventRepository $eventRepository,
        private readonly SubscriberListExport $subscriberListExport,
    ) {
        parent::__construct($logger);
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $event = $this->eventRepository->findById((string)$this->request->getAttribute('eventId'))
            ?? throw new HttpNotFoundException($this->request);

        $this->response->getBody()->write($this->subscriberListExport->toCsv($event));

        $fileName = sprintf(
            'inschrijvingen-%s-%s.csv',
            trim((string)preg_replace('/[^a-z0-9]+/', '-', strtolower($event->getName())), '-'),
            $event->getStartDate()->format('Y-m-d'),
        );

        return $this->response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', sprintf('attachment; filename="%s"', $fileName));
    }
}
