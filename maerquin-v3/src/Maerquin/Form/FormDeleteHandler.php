<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use SvenHK\Maerquin\Entity\Form;
use SvenHK\Maerquin\Exception\Form\FormDeleteException;
use SvenHK\Maerquin\Repository\EventRepository;
use SvenHK\Maerquin\Repository\FormRepository;

readonly class FormDeleteHandler
{
    public function __construct(
        private FormRepository $formRepository,
        private EventRepository $eventRepository,
    ) {
    }

    /**
     * @throws FormDeleteException
     */
    public function handle(Form $form): void
    {
        if ($this->eventRepository->findAllForForm($form->getId()) !== []) {
            throw FormDeleteException::linkedToEvents();
        }

        $form->delete();

        $this->formRepository->save($form);
    }
}
