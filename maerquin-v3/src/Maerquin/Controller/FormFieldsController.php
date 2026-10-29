<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Response;
use SvenHK\Maerquin\Exception\Form\FormFieldException;
use SvenHK\Maerquin\Form\FormFieldHandler;
use SvenHK\Maerquin\Repository\FormRepository;
use SvenHK\Maerquin\Translation\Translator;

class FormFieldsController extends Action
{
    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly FormFieldHandler $formFieldHandler,
        private readonly Translator $translator,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $form = $this->formRepository->getById((string)$this->request->getAttribute('formId'));

        $section = $form->findSection(
            (string)$this->request->getAttribute('sectionId'),
        ) ?? throw new HttpNotFoundException($this->request);

        try {
            $this->formFieldHandler->replace($form, $section, $this->request);
        } catch (FormFieldException $exception) {
            throw new HttpBadRequestException(
                $this->request,
                $this->translator->trans($exception->getTranslationKey()),
                $exception,
            );
        }

        return new Response();
    }
}
