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
use Webmozart\Assert\Assert;

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
        $characterId = (string)($this->request->getAttribute('characterId') ?? '');

        $isCreateCharacterView =
            str_ends_with($this->request->getUri()->getPath(), '/create.html');

        $isEditCharacterView = $isCreateCharacterView === false && Uuid::isValid($characterId);

        if ($isCreateCharacterView === true) {
            return $this->renderNewCharacter();
        }

        if ($isEditCharacterView === true) {
            return $this->renderEditCharacter(Uuid::fromString($characterId));
        }

        return $this->renderCharacterList();
    }

    private function renderNewCharacter(): ResponseInterface
    {
        $characterId = Uuid::uuid4();

        return $this->renderEditCharacter($characterId);
    }

    private function renderEditCharacter(UuidInterface $characterId): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $customFields = $this->customFieldRepository->findCollectionForCharacter($characterId->toString());

        $character = $this->getCharacter($characterId);

        if ($this->request->getMethod() === 'POST') {
            $this->characterFormHandler->handle(
                $this->isNewCharacter($character),
                $character,
                $customFields,
                $this->request,
            );
        }

        return $view->render(
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

    private function getCharacter(UuidInterface $characterId): Character
    {
        $character = $this->characterRepository->findById($characterId->toString());

        if ($character === null) {
            $player = $this->playerRepository->findById(
                $this->request->getQueryParams()['playerId'] ?? '',
            );

            $character = Character::createWithDefaults(
                $characterId,
                $this->getRace(),
                $player,
            );
        }

        Assert::isInstanceOf($character, Character::class);

        return $character;
    }

    private function getRace(): Race
    {
        $formResolver = FormResolver::createFromRequest($this->request);

        $raceId = $formResolver->getValue('raceId', 'character', '');

        if ($raceId !== '') {
            return $this->raceRepository->getById($raceId);
        }

        return $this->raceRepository->findByName('Mens') ??
            throw new LogicException(
                'Make sure "Mens" race exists before creating a character',
            );
    }

    private function isNewCharacter(Character $character): bool
    {
        return $this->characterRepository->findById($character->getId()) === null;
    }

    /**
     * @return array{
     *     deities: DeitiesCollection,
     *     players: PlayerCollection
     * }
     */
    private function getViewContext(): array
    {
        return [
            'deities' => new DeitiesCollection($this->deityRepository->findAll()),
            'players' => new PlayerCollection($this->playerRepository->findAllSorted()),
        ];
    }

    private function renderCharacterList(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $viewContext = array_merge(
            $this->getViewContext(),
            [
                'characters' => new CharacterCollection(
                    ...$this->characterRepository->findAllSorted(),
                ),
            ],
        );

        return $view->render(
            $this->response,
            'characters.html.twig',
            $viewContext,
        );
    }
}
