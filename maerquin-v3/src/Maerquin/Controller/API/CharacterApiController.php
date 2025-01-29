<?php

namespace SvenHK\Maerquin\Controller\API;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Repository\CharacterRepository;

class CharacterApiController extends Action
{
    /**
     * @var CharacterRepository
     */
    private EntityRepository $characterRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->characterRepository = $entityManager->getRepository(Character::class);
    }

    public function action(): ResponseInterface
    {
        return $this->respondWithData(array_map(function (Character $character) {
            return $character->serialize();
        }, $this->characterRepository->findAllSorted()));
    }
}
