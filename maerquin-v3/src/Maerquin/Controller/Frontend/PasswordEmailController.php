<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\Frontend;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Form\FormResolver;
use SvenHK\Maerquin\Form\PasswordEmailFormHandler;
use SvenHK\Maerquin\Mail\AccountMailer;

class PasswordEmailController extends Action
{
    public function __construct(
        private readonly PasswordEmailFormHandler $passwordEmailFormHandler,
        private readonly AccountMailer $accountMailer,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        if ($this->request->getMethod() === 'POST') {
            $user = $this->passwordEmailFormHandler->handle($this->request);

            if ($user !== null) {
                $this->accountMailer->sendPasswordSetup(Twig::fromRequest($this->request), $user);
            }

            return $this->renderPasswordEmailView(sent: true);
        }

        return $this->renderPasswordEmailView(sent: false);
    }

    private function renderPasswordEmailView(bool $sent): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'Frontend/password_email.html.twig',
            [
                'form' => FormResolver::createFromRequest($this->request),
                'sent' => $sent,
            ],
        );
    }
}
