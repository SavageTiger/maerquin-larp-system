<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Entity\CustomField;
use SvenHK\Maerquin\Entity\Deity;
use SvenHK\Maerquin\Entity\Player;
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Model\CustomFieldCollection;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\CustomFieldRepository;
use SvenHK\Maerquin\Repository\DeityRepository;
use SvenHK\Maerquin\Repository\PlayerRepository;
use SvenHK\Maerquin\Repository\RaceRepository;

class CharacterFormHandler
{
    /**
     * @var CharacterRepository
     */
    private EntityRepository $characterRepository;

    /**
     * @var PlayerRepository
     */
    private EntityRepository $playerRepository;

    /**
     * @var RaceRepository
     */
    private EntityRepository $raceRepository;

    /**
     * @var DeityRepository
     */
    private EntityRepository $deityRepository;

    /**
     * @var CustomFieldRepository
     */
    private EntityRepository $customFieldRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->characterRepository = $entityManager->getRepository(Character::class);
        $this->playerRepository = $entityManager->getRepository(Player::class);
        $this->raceRepository = $entityManager->getRepository(Race::class);
        $this->deityRepository = $entityManager->getRepository(Deity::class);
        $this->customFieldRepository = $entityManager->getRepository(CustomField::class);
    }

    public function handle(
        string $characterId,
        CustomFieldCollection $customFields,
        Request $request,
    ): void {
        $formResolver = FormResolver::createFromRequest($request);

        $character = $this->characterRepository->getById($characterId);

        $player = $this->playerRepository->find(
            $formResolver->getValue('playerId', 'character'),
        );

        $race = $this->raceRepository->getById(
            $formResolver->getValue('raceId', 'character'),
        );

        $primaryDeity = $this->deityRepository->find(
            $formResolver->getValue('primaryDeityId', 'character'),
        );

        $secondaryDeity = $this->deityRepository->find(
            $formResolver->getValue('secondaryDeityId', 'character'),
        );

        foreach ($customFields->getCustomFields() as $customField) {
            $this->customFieldRepository->updateFieldValue(
                $customField->getId(),
                $characterId,
                $formResolver->getValue($customField->getId(), 'character'),
            );
        }

        $character->updateCharacter(
            $formResolver->getValue('name', 'character'),
            $player,
            $race,
            $formResolver->getBoolean('isDeceased', 'character'),
            $primaryDeity,
            $secondaryDeity,
            $formResolver->getValue('guild', 'character'),
            $formResolver->getValue('title', 'character'),
            $formResolver->getValue('occupation', 'character'),
            $formResolver->getValue('birthplace', 'character'),
            $formResolver->getValue('notes', 'character'),
        );

        $this->characterRepository->save($character);
    }
}
