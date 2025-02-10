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
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Model\CharacterCollection;
use SvenHK\Maerquin\Model\DeitiesCollection;
use SvenHK\Maerquin\Model\PlayerCollection;
use SvenHK\Maerquin\Model\RaceCollection;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\DeityRepository;
use SvenHK\Maerquin\Repository\PlayerRepository;
use SvenHK\Maerquin\Repository\RaceRepository;

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

    /**
     * @var RaceRepository
     */
    private EntityRepository $raceRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->characterRepository = $entityManager->getRepository(Character::class);
        $this->deityRepository = $entityManager->getRepository(Deity::class);
        $this->playerRepository = $entityManager->getRepository(Player::class);
        $this->raceRepository = $entityManager->getRepository(Race::class);
    }

    public function action() : ResponseInterface
    {
        $view = Twig::fromRequest($this->request);
        $viewContext = ['deities' => new DeitiesCollection($this->deityRepository->findAll())];

        $characterId = $this->request->getAttribute('characterId');

        if (is_string($characterId) && Uuid::isValid($characterId)) {
            return $view->render(
                $this->response,
                'character.html.twig', array_merge($viewContext,
                    [
                        'character' => $this->characterRepository->getById($characterId),
                        'races' => new RaceCollection($this->raceRepository->findAllSorted()),
                    ]
                )
            );
        }

        return $view->render(
            $this->response,
            'characters.html.twig', array_merge($viewContext, [
                'players' => new PlayerCollection($this->playerRepository->findAll()),
                'characters' => new CharacterCollection($this->characterRepository->findAllSorted()),
            ])
        );
    }
}
