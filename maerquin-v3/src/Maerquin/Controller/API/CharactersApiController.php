<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\API;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Model\CharacterCollection;
use SvenHK\Maerquin\Repository\CharacterRepository;

class CharactersApiController extends Action
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
        $compact = (($this->request->getQueryParams()['compact'] ?? '') === 'true');

        return $this->respondWithData(
            new CharacterCollection($this->characterRepository->findAllSorted())->serialize(
                $compact,
            ),
        );
    }
}
