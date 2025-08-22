<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use Override;
use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Token;
use SvenHK\Maerquin\Entity\User;
use SvenHK\Maerquin\Form\FormResolver;
use SvenHK\Maerquin\Repository\TokenRepository;
use SvenHK\Maerquin\Repository\UserRepository;
use SvenHK\Maerquin\Session\Session;

class LoginController extends Action
{
    use RedirectTo;

    /**
     * @var UserRepository
     */
    private EntityRepository $userRepository;

    /**
     * @var TokenRepository
     */
    private EntityRepository $tokenRepository;

    public function __construct(
        private Session $session,
        EntityManager $entityManager,
    ) {
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->tokenRepository = $entityManager->getRepository(Token::class);
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

                return $this->redirectTo('/home.html', $rememberMeCookie);
            }
        }

        if ($this->loginRememberMeCookie() === true) {
            return $this->redirectTo('/home.html');
        }

        return $view->render(
            $this->response,
            'login.html.twig',
            ['form' => $form, 'loginError' => $loginError ?? null],
        );
    }

    public function login(string $username, string $password): false | string
    {
        $user = $this->userRepository->findByUsername($username);

        if ($user === null) {
            return 'User not found';
        }

        if ($user->checkPassword($password) === false) {
            return 'Password incorrect';
        }

        $this->session->setUser($user);

        return false;
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
            $this->session->setUser($rememberMeToken->getUser());

            return true;
        }

        return false;
    }
}
