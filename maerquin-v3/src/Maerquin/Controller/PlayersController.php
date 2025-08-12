<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\Player as PlayerEntity;
use SvenHK\Maerquin\Form\PlayerFormHandler;
use SvenHK\Maerquin\Model\CharacterCollection;
use SvenHK\Maerquin\Model\Player;
use SvenHK\Maerquin\Model\PlayerCollection;

class PlayersController extends Action
{
    private EntityRepository $playerRepository;
    private EntityRepository $characterRepository;

    public function __construct(
        EntityManager $entityManager,
        private PlayerFormHandler $formHandler,
    ) {
        $this->playerRepository = $entityManager->getRepository(PlayerEntity::class);
        $this->characterRepository = $entityManager->getRepository(Character::class);
    }

    public function action(): ResponseInterface
    {
        $userId = $this->request->getAttribute('userId');

        if (str_contains($this->request->getUri()->getPath(), 'create.html') === true) {
            $userId = Uuid::uuid4()->toString();
        }

        if (is_string($userId) && Uuid::isValid($userId)) {
            return $this->renderEditView(Uuid::fromString($userId));
        }

        return $this->renderListView();
    }

    private function renderEditView(UuidInterface $userId): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $player = $this->getPlayers($userId);

        if ($this->request->getMethod() === 'POST') {
            $this->formHandler->handle($player, $this->request);
        }

        return $view->render(
            $this->response,
            'player.html.twig',
            [
                'player' => $player,
                'playerCharacters' => new CharacterCollection(
                    $this->characterRepository->forPlayer($player->getId()),
                ),
                'persisted' => str_contains($this->request->getUri()->getPath(), '/persisted/'),
            ],
        );
    }

    private function getPlayers(UuidInterface $userId): Player
    {
        $player = $this->playerRepository->find($userId->toString());

        if ($player === null) {
            $player = Player::create($userId);
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
