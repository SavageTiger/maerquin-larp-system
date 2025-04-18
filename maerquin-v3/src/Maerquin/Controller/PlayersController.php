<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\Player;
use SvenHK\Maerquin\Model\CharacterCollection;
use SvenHK\Maerquin\Model\PlayerCollection;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\PlayerRepository;

class PlayersController extends Action
{
    /**
     * @var PlayerRepository
     */
    private EntityRepository $playerRepository;

    /**
     * @var CharacterRepository
     */
    private EntityRepository $characterRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->playerRepository = $entityManager->getRepository(Player::class);
        $this->characterRepository = $entityManager->getRepository(Character::class);
    }

    public function action() : ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $userId = $this->request->getAttribute('userId');

        if (is_string($userId) && Uuid::isValid($userId)) {
            $player = $this->playerRepository->getById($userId);

            return $view->render(
                $this->response,
                'player.html.twig',
                [
                    'player' => $player,
                    'playerCharacters' => new CharacterCollection(
                        $this->characterRepository->forPlayer($player->getId()),
                    ),
                ],
            );
        }

        return $view->render(
            $this->response,
            'players.html.twig',
            [
                'players' => new PlayerCollection($this->playerRepository->findAllSorted()),
            ],
        );
    }
}
