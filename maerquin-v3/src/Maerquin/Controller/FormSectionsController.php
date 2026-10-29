<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpMethodNotAllowedException;
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

        $isSectionRequest = $this->request->getAttribute('sectionId') !== null;

        try {
            return match (true) {
                $this->request->getMethod() === 'GET' && $isSectionRequest => $this->renderSectionView($form),
                $this->request->getMethod() === 'POST' && $isSectionRequest => $this->deleteSection($form),
                $this->request->getMethod() === 'POST' => $this->addSection($form),
                default => throw new HttpMethodNotAllowedException($this->request),
            };
        } catch (FormSectionException $exception) {
            throw new HttpBadRequestException(
                $this->request,
                $this->translator->trans($exception->getTranslationKey()),
                $exception,
            );
        }
    }

    private function renderSectionView(Form $form): ResponseInterface
    {
        $section = $this->findSection($form);

        return Twig::fromRequest($this->request)->render(
            $this->response,
            'form_section.html.twig',
            [
                'form' => $form,
                'section' => $section,
                'fieldTypes' => FormFieldType::cases(),
                'maxFieldsPerRow' => FormSection::MAX_FIELDS_PER_ROW,
            ],
        );
    }

    private function deleteSection(Form $form): ResponseInterface
    {
        $this->formSectionHandler->delete($form, $this->findSection($form));

        return new Response();
    }

    private function findSection(Form $form): FormSection
    {
        return $form->findSection((string)$this->request->getAttribute('sectionId'))
            ?? throw new HttpNotFoundException($this->request);
    }

    private function addSection(Form $form): ResponseInterface
    {
        $this->formSectionHandler->handle($form, $this->request);

        return new Response();
    }
}
