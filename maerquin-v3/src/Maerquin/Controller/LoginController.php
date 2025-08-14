<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\User;
use SvenHK\Maerquin\Form\FormResolver;
use SvenHK\Maerquin\Repository\UserRepository;
use SvenHK\Maerquin\Session\Session;

class LoginController extends Action
{
    use RedirectTo;

    /**
     * @var UserRepository
     */
    private EntityRepository $userRepository;

    public function __construct(
        private Session $session,
        EntityManager $entityManager,
    ) {
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    public function action(): ResponseInterface
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
        $user = $this->userRepository->findByUsername($username);

        $rememberMeToken = $user->generateRememberToken();

        $this->userRepository->save($user);

        return $rememberMeToken;
    }

    private function loginRememberMeCookie(): bool
    {
        $rememberMeToken = $this->request->getCookieParams()['RMT'] ?? null;

        if (is_string($rememberMeToken) === false) {
            return false;
        }

        $user = $this->userRepository->findByValidRememberMeToken($rememberMeToken);

        if ($user === null) {
            return false;
        }

        $this->session->setUser($user);

        return true;
    }
}
