<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Form;
use SvenHK\Maerquin\Entity\FormSection;
use SvenHK\Maerquin\Exception\Form\FormSectionException;
use SvenHK\Maerquin\Form\FormSectionHandler;
use SvenHK\Maerquin\Model\FormFieldType;
use SvenHK\Maerquin\Repository\FormRepository;
use SvenHK\Maerquin\Translation\Translator;

class FormSectionsController extends Action
{
    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly FormSectionHandler $formSectionHandler,
        private readonly Translator $translator,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $form = $this->formRepository->getById((string)$this->request->getAttribute('formId'));

        if ($this->request->getMethod() === 'GET') {
            return $this->renderSectionView($form);
        }

        if ($this->request->getAttribute('sectionId') !== null) {
            $this->formSectionHandler->delete($form, $this->findSection($form));

            return new Response();
        }

        try {
            $this->formSectionHandler->handle($form, $this->request);
        } catch (FormSectionException $exception) {
            throw new HttpBadRequestException(
                $this->request,
                $this->translator->trans($exception->getTranslationKey()),
                $exception,
            );
        }

        return new Response();
    }

    private function renderSectionView(Form $form): ResponseInterface
    {
        return Twig::fromRequest($this->request)->render(
            $this->response,
            'form_section.html.twig',
            [
                'form' => $form,
                'section' => $this->findSection($form),
                'fieldTypes' => FormFieldType::cases(),
                'maxFieldsPerRow' => FormSection::MAX_FIELDS_PER_ROW,
            ],
        );
    }

    private function findSection(Form $form): FormSection
    {
        return $form->findSection((string)$this->request->getAttribute('sectionId'))
            ?? throw new HttpNotFoundException($this->request);
    }
}
