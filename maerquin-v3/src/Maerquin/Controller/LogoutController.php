<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use Override;
use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface;
use SvenHK\Maerquin\Session\Session;

class LogoutController extends Action
{
    use RedirectTo;

    public function __construct(
        private Session $session,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $this->session->unsetUser();

        return $this->redirectTo('/', '');
    }
}
