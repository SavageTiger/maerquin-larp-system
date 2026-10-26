<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\Frontend;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Controller\RedirectTo;
use SvenHK\Maerquin\Exception\MaerquinEmailAlreadyInUseException;
use SvenHK\Maerquin\Exception\MaerquinRegistrationException;
use SvenHK\Maerquin\Form\FormResolver;
use SvenHK\Maerquin\Form\RegistrationFormHandler;
use SvenHK\Maerquin\Mail\AccountMailer;
use SvenHK\Maerquin\Session\Session;

class RegisterController extends Action
{
    use RedirectTo;

    public function __construct(
        private readonly Session $session,
        private readonly RegistrationFormHandler $registrationFormHandler,
        private readonly AccountMailer $accountMailer,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        if ($this->session->getUser()->isAnonymous() === false) {
            return $this->redirectTo('/subscriptions.html');
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->handleRegistration();
        }

        return $this->renderRegisterView('form');
    }

    private function handleRegistration(): ResponseInterface
    {
        try {
            $user = $this->registrationFormHandler->handle($this->request);
        } catch (MaerquinRegistrationException $exception) {
            return $this->renderRegisterView('form', $exception->getMessage());
        } catch (MaerquinEmailAlreadyInUseException) {
            return $this->renderRegisterView('emailTaken');
        }

        $this->accountMailer->sendEmailVerification(Twig::fromRequest($this->request), $user);

        return $this->renderRegisterView('registered');
    }

    private function renderRegisterView(string $state, null | string $error = null): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'Frontend/register.html.twig',
            [
                'form' => FormResolver::createFromRequest($this->request),
                'state' => $state,
                'error' => $error,
            ],
        );
    }
}
