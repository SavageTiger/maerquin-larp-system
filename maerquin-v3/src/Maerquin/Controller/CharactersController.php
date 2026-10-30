<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use LogicException;
use Override;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Form\CharacterFormHandler;
use SvenHK\Maerquin\Form\FormResolver;
use SvenHK\Maerquin\Model\CharacterCollection;
use SvenHK\Maerquin\Model\DeitiesCollection;
use SvenHK\Maerquin\Model\PlayerCollection;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\CustomFieldRepository;
use SvenHK\Maerquin\Repository\DeityRepository;
use SvenHK\Maerquin\Repository\PlayerRepository;
use SvenHK\Maerquin\Repository\RaceRepository;

class CharactersController extends Action
{
    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly DeityRepository $deityRepository,
        private readonly PlayerRepository $playerRepository,
        private readonly RaceRepository $raceRepository,
        private readonly CustomFieldRepository $customFieldRepository,
        private readonly CharacterFormHandler $characterFormHandler,
        private readonly CharacterViewContext $characterViewContext,
    ) {
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        if (str_ends_with($this->request->getUri()->getPath(), '/create.html')) {
            return $this->renderEditCharacter(Uuid::uuid4());
        }

        $characterId = (string)($this->request->getAttribute('characterId') ?? '');

        if (Uuid::isValid($characterId)) {
            return $this->renderEditCharacter(Uuid::fromString($characterId));
        }

        return $this->renderCharacterList();
    }

    private function renderEditCharacter(UuidInterface $characterId): ResponseInterface
    {
        $customFields = $this->customFieldRepository->findCollectionForCharacter($characterId->toString());

        $existingCharacter = $this->characterRepository->findById($characterId->toString());

        $character = $existingCharacter ?? $this->createCharacter($characterId);

        if ($this->request->getMethod() === 'POST') {
            $this->characterFormHandler->handle($existingCharacter === null, $character, $customFields, $this->request);
        }

        return Twig::fromRequest($this->request)->render(
            $this->response,
            'character.html.twig',
            [
                ...$this->characterViewContext->forCharacter(
                    $character,
                    new PlayerCollection($this->playerRepository->findAllSorted()),
                    $customFields,
                ),
                'persisted' => str_contains($this->request->getUri()->getPath(), '/persisted/'),
            ],
        );
    }

    private function createCharacter(UuidInterface $characterId): Character
    {
        return Character::createWithDefaults(
            $characterId,
            $this->getRace(),
            $this->playerRepository->findById($this->request->getQueryParams()['playerId'] ?? ''),
        );
    }

    private function getRace(): Race
    {
        $raceId = FormResolver::createFromRequest($this->request)->getValue('raceId', 'character', '');

        if ($raceId !== '') {
            return $this->raceRepository->getById($raceId);
        }

        return $this->raceRepository->findByName('Mens') ??
            throw new LogicException('Make sure "Mens" race exists before creating a character');
    }

    private function renderCharacterList(): ResponseInterface
    {
        return Twig::fromRequest($this->request)->render(
            $this->response,
            'characters.html.twig',
            [
                'characters' => new CharacterCollection(...$this->characterRepository->findAllSorted()),
                'deities' => new DeitiesCollection($this->deityRepository->findAll()),
                'players' => new PlayerCollection($this->playerRepository->findAllSorted()),
            ],
        );
    }
}
