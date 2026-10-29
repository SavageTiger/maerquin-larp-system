<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Response;
use SvenHK\Maerquin\Exception\Form\FormDeleteException;
use SvenHK\Maerquin\Form\FormDeleteHandler;
use SvenHK\Maerquin\Repository\FormRepository;
use SvenHK\Maerquin\Translation\Translator;

class FormDeleteController extends Action
{
    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly FormDeleteHandler $formDeleteHandler,
        private readonly Translator $translator,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $form = $this->formRepository->getById((string)$this->request->getAttribute('formId'));

        try {
            $this->formDeleteHandler->handle($form);
        } catch (FormDeleteException $exception) {
            throw new HttpBadRequestException(
                $this->request,
                $this->translator->trans($exception->getTranslationKey()),
                $exception,
            );
        }

        return new Response();
    }
}
