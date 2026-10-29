<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Form;
use SvenHK\Maerquin\Entity\FormSection;
use SvenHK\Maerquin\Exception\Form\FormSectionException;
use SvenHK\Maerquin\Exception\Request\MissingFormFieldException;
use SvenHK\Maerquin\Repository\FormRepository;

readonly class FormSectionHandler
{
    public function __construct(
        private FormRepository $formRepository,
    ) {
    }

    /**
     * @throws MissingFormFieldException
     * @throws FormSectionException
     */
    public function handle(Form $form, Request $request): void
    {
        $formResolver = FormResolver::createFromRequest($request);

        $form->addSection($formResolver->getValue('title', 'section'));

        $this->formRepository->save($form);
    }

    /**
     * @throws FormSectionException
     */
    public function delete(Form $form, FormSection $section): void
    {
        $form->deleteSection($section);

        $this->formRepository->save($form);
    }
}
