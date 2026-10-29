<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Form;
use SvenHK\Maerquin\Exception\MaerquinFormSectionException;
use SvenHK\Maerquin\Repository\FormRepository;

readonly class FormSectionHandler
{
    public function __construct(
        private FormRepository $formRepository,
    ) {
    }

    /**
     * @throws MissingFormFieldException
     * @throws MaerquinFormSectionException
     */
    public function handle(Form $form, Request $request): void
    {
        $formResolver = FormResolver::createFromRequest($request);

        $form->addSection($formResolver->getValue('title', 'section'));

        $this->formRepository->save($form);
    }
}
