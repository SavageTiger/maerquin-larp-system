<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Status\StatusService;

class StatusController extends Action
{
    public function __construct(
        private readonly StatusService $statusService,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'status.html.twig',
            [
                'status' => $this->statusService->getStatus(),
            ],
        );
    }
}
