<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Form\PlayerFormHandler;
use SvenHK\Maerquin\Model\CharacterCollection;
use SvenHK\Maerquin\Model\Player;
use SvenHK\Maerquin\Model\PlayerCollection;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\PlayerRepository;
use SvenHK\Maerquin\Repository\UserRepository;

class PlayersController extends Action
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly CharacterRepository $characterRepository,
        private readonly UserRepository $userRepository,
        private readonly PlayerFormHandler $formHandler,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $playerId = $this->request->getAttribute('playerId');

        if (str_contains($this->request->getUri()->getPath(), 'create.html') === true) {
            $playerId = Uuid::uuid4()->toString();
        }

        if (is_string($playerId) && Uuid::isValid($playerId)) {
            return $this->renderEditView(Uuid::fromString($playerId));
        }

        return $this->renderListView();
    }

    private function renderEditView(UuidInterface $playerId): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $player = $this->getPlayers($playerId);

        if ($this->request->getMethod() === 'POST') {
            $this->formHandler->handle($player, $this->request);
        }

        $account = $this->userRepository->findByPlayer($player->getId());

        return $view->render(
            $this->response,
            'player.html.twig',
            [
                'player' => $player,
                'account' => $account,
                'playerCharacters' => new CharacterCollection(
                    ...$this->characterRepository->forPlayer($player->getId()),
                ),
                'persisted' => str_contains($this->request->getUri()->getPath(), '/persisted/'),
            ],
        );
    }

    private function getPlayers(UuidInterface $playerId): Player
    {
        $player = $this->playerRepository->findById($playerId->toString());

        if ($player === null) {
            return Player::create($playerId);
        }

        return $player;
    }

    private function renderListView(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'players.html.twig',
            [
                'players' => new PlayerCollection(
                    $this->playerRepository->findAllSorted(),
                ),
            ],
        );
    }
}
