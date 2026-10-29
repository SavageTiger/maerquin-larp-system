<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Psr7\Response;
use SvenHK\Maerquin\Entity\Form;
use SvenHK\Maerquin\Exception\MaerquinFormSectionException;
use SvenHK\Maerquin\Form\FormSectionHandler;
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
                'POST' => $this->addSection($form),
                default => throw new HttpMethodNotAllowedException($this->request),
            };
        } catch (MaerquinFormSectionException $exception) {
            throw new HttpBadRequestException(
                $this->request,
                $this->translator->trans($exception->getTranslationKey()),
                $exception,
            );
        }
    }

    private function addSection(Form $form): ResponseInterface
    {
        $this->formSectionAddHandler->handle($form, $this->request);

        return new Response();
    }
}
