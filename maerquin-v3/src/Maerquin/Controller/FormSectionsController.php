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
use SvenHK\Maerquin\Exception\Form\FormSectionException;
use SvenHK\Maerquin\Form\FormSectionHandler;
use SvenHK\Maerquin\Model\FormFieldType;
use SvenHK\Maerquin\Model\FormSection as FormSectionModel;
use SvenHK\Maerquin\Repository\FormRepository;
use SvenHK\Maerquin\Translation\Translator;

class FormSectionsController extends Action
{
    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly FormSectionHandler $formSectionAddHandler,
        private readonly Translator $translator,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $form = $this->formRepository->getById((string)$this->request->getAttribute('formId'));

        try {
            return match ($this->request->getMethod()) {
                'GET' => $this->renderSectionView($form),
                'POST' => $this->addSection($form),
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
        $section = $form->findSection((string)$this->request->getAttribute('sectionId'));

        if ($section === null) {
            throw new HttpNotFoundException($this->request);
        }

        return Twig::fromRequest($this->request)->render(
            $this->response,
            'form_section.html.twig',
            [
                'form' => $form,
                'section' => $section,
                'fieldTypes' => FormFieldType::cases(),
                'maxFieldsPerRow' => FormSectionModel::MAX_FIELDS_PER_ROW,
            ],
        );
    }

    private function addSection(Form $form): ResponseInterface
    {
        $this->formSectionAddHandler->handle($form, $this->request);

        return new Response();
    }
}
