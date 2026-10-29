<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use SvenHK\Maerquin\Entity\Form;
use SvenHK\Maerquin\Exception\MaerquinFormDeleteException;
use SvenHK\Maerquin\Repository\EventRepository;
use SvenHK\Maerquin\Repository\FormRepository;
use SvenHK\Maerquin\Repository\FormSubmissionRepository;

readonly class FormDeleteHandler
{
    public function __construct(
        private FormRepository $formRepository,
        private EventRepository $eventRepository,
        private FormSubmissionRepository $formSubmissionRepository,
    ) {
    }

    /**
     * @throws MaerquinFormDeleteException
     */
    public function handle(Form $form): void
    {
        if ($this->eventRepository->findAllForForm($form->getId()) !== []) {
            throw MaerquinFormDeleteException::linkedToEvents();
        }

        if ($this->formSubmissionRepository->findAllForForm($form->getId()) !== []) {
            throw MaerquinFormDeleteException::hasSubmissions();
        }

        $this->formRepository->delete($form);
    }
}
