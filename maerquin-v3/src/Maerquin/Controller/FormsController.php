<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Form\FormFormHandler;
use SvenHK\Maerquin\Model\Form as FormModel;
use SvenHK\Maerquin\Repository\EventRepository;
use SvenHK\Maerquin\Repository\FormRepository;

class FormsController extends Action
{
    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly FormFormHandler $formHandler,
        private readonly EventRepository $eventRepository,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $formId = $this->request->getAttribute('formId');

        if (str_contains($this->request->getUri()->getPath(), 'create.html') === true) {
            $formId = Uuid::uuid4()->toString();
        }

        if (is_string($formId) && Uuid::isValid($formId)) {
            return $this->renderEditView(Uuid::fromString($formId));
        }

        return $this->renderListView();
    }

    private function renderEditView(UuidInterface $formId): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $form = $this->formRepository->findById($formId->toString()) ?? FormModel::create($formId);

        if ($this->request->getMethod() === 'POST') {
            $this->formHandler->handle($form, $this->request);
        }

        return $view->render(
            $this->response,
            'form.html.twig',
            [
                'form' => $form,
                'linkedEvents' => $this->eventRepository->findAllForForm($formId->toString()),
                'persisted' => str_contains($this->request->getUri()->getPath(), '/persisted/'),
            ],
        );
    }

    private function renderListView(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'forms.html.twig',
            [
                'forms' => $this->formRepository->findAllSorted(),
            ],
        );
    }
}
