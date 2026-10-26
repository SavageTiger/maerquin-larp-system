<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\API;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface;
use SvenHK\Maerquin\Model\CharacterCollection;
use SvenHK\Maerquin\Repository\CharacterRepository;

class CharactersApiController extends Action
{
    public function __construct(
        private readonly CharacterRepository $characterRepository,
    ) {
    }

    public function action(): ResponseInterface
    {
        $compact = (($this->request->getQueryParams()['compact'] ?? '') === 'true');

        return $this->respondWithData(
            new CharacterCollection(
                ...$this->characterRepository->findAllSorted(),
            )->serialize(
                $compact,
            ),
        );
    }
}
