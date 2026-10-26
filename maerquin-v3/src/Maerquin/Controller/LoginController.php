<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Token;
use SvenHK\Maerquin\Entity\User;
use SvenHK\Maerquin\Form\FormResolver;
use SvenHK\Maerquin\Mail\AccountMailer;
use SvenHK\Maerquin\Repository\TokenRepository;
use SvenHK\Maerquin\Repository\UserRepository;
use SvenHK\Maerquin\Session\Session;
use SvenHK\Maerquin\Translation\Translator;

class LoginController extends Action
{
    use RedirectTo;

    public function __construct(
        private Session $session,
        private readonly UserRepository $userRepository,
        private readonly TokenRepository $tokenRepository,
        private readonly AccountMailer $accountMailer,
        private readonly Translator $translator,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);
        $form = FormResolver::createFromRequest($this->request);

        if ($this->request->getMethod() === 'POST') {
            $loginError = $this->login(
                $form->getValue('username'),
                $form->getValue('password'),
            );

            if ($loginError === false) {
                $rememberMeCookie = null;

                if ($form->getBoolean('rememberMe', default: false) === true) {
                    $rememberMeCookie = $this->getRememberMeToken($form->getValue('username'));
                }

                return $this->redirectTo($this->getRedirectPath(), $rememberMeCookie);
            }
        }

        if ($this->loginRememberMeCookie() === true) {
            return $this->redirectTo($this->getRedirectPath());
        }

        return $view->render(
            $this->response,
            'login.html.twig',
            ['form' => $form, 'loginError' => $loginError ?? null],
        );
    }

    private function getRedirectPath(): string
    {
        $redirect = $this->request->getQueryParams()['redirect'] ?? null;

        if (is_string($redirect) === false || preg_match('#^/(?![/\\\])[A-Za-z0-9/._~?=&%-]*$#', $redirect) !== 1) {
            return '/home.html';
        }

        return $redirect;
    }

    public function login(string $username, string $password): false | string
    {
        $user = $this->userRepository->findByUsername($username);

        if ($user === null) {
            return $this->translator->trans('login.error.user_not_found');
        }

        if ($user->checkPassword($password) === false) {
            return $this->translator->trans('login.error.password_incorrect');
        }

        if ($user->isEmailVerified() === false) {
            $this->accountMailer->sendEmailVerification(Twig::fromRequest($this->request), $user);

            return $this->translator->trans('login.error.email_not_verified');
        }

        $this->loginUser($user);

        return false;
    }

    private function loginUser(User $user): void
    {
        $this->session->setUser($user);

        $user->loggedIn();

        $this->userRepository->save($user);
    }

    private function getRememberMeToken(string $username): string
    {
        $token = Token::generateForUser(
            $this->userRepository->findByUsername($username),
        );

        $this->tokenRepository->save($token);

        return $token->getUnhashedValue();
    }

    private function loginRememberMeCookie(): bool
    {
        $rememberMeToken = $this->request->getCookieParams()['RMT'] ?? null;

        if (is_string($rememberMeToken) === false) {
            return false;
        }

        $rememberMeToken = $this->tokenRepository->findByCookieValue($rememberMeToken);

        if ($rememberMeToken !== null && $rememberMeToken->isValid() === true) {
            $this->loginUser($rememberMeToken->getUser());

            return true;
        }

        return false;
    }
}
