<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\Frontend;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;
use SvenHK\Maerquin\Controller\CharacterViewContext;
use SvenHK\Maerquin\Controller\RedirectTo;
use SvenHK\Maerquin\Model\PlayerCollection;
use SvenHK\Maerquin\Model\User;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\CustomFieldRepository;
use SvenHK\Maerquin\Session\Session;

class CharactersController extends Action
{
    use RedirectTo;

    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly CustomFieldRepository $customFieldRepository,
        private readonly CharacterViewContext $characterViewContext,
        private readonly Session $session,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $user = $this->session->getUser();

        if ($user instanceof User === false) {
            return $this->redirectTo('/login.html?redirect=' . $this->request->getUri()->getPath());
        }

        $characterId = $this->request->getAttribute('characterId');

        if (is_string($characterId)) {
            return $this->renderCharacterView($characterId, $user);
        }

        return Twig::fromRequest($this->request)->render(
            $this->response,
            'Frontend/characters.html.twig',
            [
                'characters' => $this->characterRepository->forPlayer($user->getPlayer()->getId()),
            ],
        );
    }

    private function renderCharacterView(string $characterId, User $user): ResponseInterface
    {
        $character = $this->characterRepository->findById($characterId);

        if ($character === null || $character->isOwnedBy($user->getPlayer()) === false) {
            throw new HttpNotFoundException($this->request);
        }

        return Twig::fromRequest($this->request)->render(
            $this->response,
            'character.html.twig',
            [
                ...$this->characterViewContext->forCharacter(
                    $character,
                    new PlayerCollection([$user->getPlayer()]),
                    $this->customFieldRepository->findCollectionForCharacter($characterId),
                ),
                'readOnly' => true,
                'persisted' => false,
            ],
        );
    }
}
