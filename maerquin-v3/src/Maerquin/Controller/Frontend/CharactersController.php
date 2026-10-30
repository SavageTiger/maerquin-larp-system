<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\Frontend;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Controller\RedirectTo;
use SvenHK\Maerquin\Model\User;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Session\Session;

class CharactersController extends Action
{
    use RedirectTo;

    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly Session $session,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $user = $this->session->getUser();

        if ($user instanceof User === false) {
            return $this->redirectTo('/login.html?redirect=/characters.html');
        }

        return Twig::fromRequest($this->request)->render(
            $this->response,
            'Frontend/characters.html.twig',
            [
                'characters' => $this->characterRepository->forPlayer($user->getPlayer()->getId()),
            ],
        );
    }
}
