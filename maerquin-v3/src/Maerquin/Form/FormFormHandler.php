<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Form;
use SvenHK\Maerquin\Exception\Request\MissingFormFieldException;
use SvenHK\Maerquin\Repository\FormRepository;

readonly class FormFormHandler
{
    public function __construct(
        private FormRepository $formRepository,
    ) {
    }

    /**
     * @throws MissingFormFieldException
     */
    public function handle(Form $form, Request $request): void
    {
        $formResolver = FormResolver::createFromRequest($request);

        $name = $formResolver->getValue('name', 'form');

        if (trim($name) === '') {
            $name = '(Naamloos)';
        }

        $form->rename($name);

        $this->formRepository->save($form);
    }
}
