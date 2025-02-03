<?php

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\Deity;
use SvenHK\Maerquin\Entity\Player;
use SvenHK\Maerquin\Model\CharacterCollection;
use SvenHK\Maerquin\Model\DietiesCollection;
use SvenHK\Maerquin\Model\PlayerCollection;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\DeityRepository;
use SvenHK\Maerquin\Repository\PlayerRepository;

class CharactersController extends Action
{
    /**
     * @var CharacterRepository
     */
    private EntityRepository $characterRepository;

    /**
     * @var DeityRepository
     */
    private EntityRepository $deityRepository;

    /**
     * @var PlayerRepository
     */
    private EntityRepository $playerRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->characterRepository = $entityManager->getRepository(Character::class);
        $this->deityRepository = $entityManager->getRepository(Deity::class);
        $this->playerRepository = $entityManager->getRepository(Player::class);
    }

    public function action(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $characterId = $this->request->getAttribute('characterId');

        if (is_string($characterId) && Uuid::isValid($characterId)) {
            return $view->render(
                $this->response,
                'character.html.twig',
                [
                    'character' => $this->characterRepository->getById($characterId),
                    'deities' => $this->deityRepository->findAll(),
                ]
            );

        }

        return $view->render(
            $this->response,
            'characters.html.twig',
            [
                'players' => new PlayerCollection($this->playerRepository->findAll()),
                'characters' => new CharacterCollection($this->characterRepository->findAllSorted()),
                'dieties' => new DietiesCollection($this->deityRepository->findAll())
            ]
        );
    }
}
