<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Repository\FormRepository;

class FormsController extends Action
{
    public function __construct(
        private readonly FormRepository $formRepository,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'forms.html.twig',
            [
                'forms' => $this->formRepository->findAllSorted(),
            ],
        );
    }
}
