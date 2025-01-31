<?php

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Player;
use SvenHK\Maerquin\Model\CharacterCollection;
use SvenHK\Maerquin\Repository\CharacterRepository;

class CharactersController extends Action
{
    /**
     * @var CharacterRepository
     */
    private EntityRepository $characterRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->characterRepository = $entityManager->getRepository(Player::class);
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
                    'player' => $this->characterRepository->getById($characterId)
                ]
            );

        }

        return $view->render(
            $this->response,
            'characters.html.twig',
            [
                'characters' => new CharacterCollection($this->characterRepository->findAllSorted())
            ]
        );
    }
}
