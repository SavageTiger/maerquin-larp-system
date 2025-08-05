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
use SvenHK\Maerquin\Entity\Skill;
use SvenHK\Maerquin\Model\CustomFieldCollection;
use SvenHK\Maerquin\Model\SkillLink;
use SvenHK\Maerquin\Model\SkillLinkCollection;
use SvenHK\Maerquin\Repository\CharacterRepository;
use SvenHK\Maerquin\Repository\CustomFieldRepository;
use SvenHK\Maerquin\Repository\DeityRepository;
use SvenHK\Maerquin\Repository\PlayerRepository;
use SvenHK\Maerquin\Repository\RaceRepository;
use SvenHK\Maerquin\Repository\SkillRepository;
use Webmozart\Assert\Assert;

class CharacterFormHandler
{
    /**
     * @var CharacterRepository
     */
    private EntityRepository $characterRepository;

    /**
     * @var SkillRepository
     */
    private EntityRepository $skillRepository;

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
        $this->skillRepository = $entityManager->getRepository(Skill::class);
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
            $this->createLinkedSkillCollection($formResolver, $character),
        );

        $this->characterRepository->save($character);
    }

    private function createLinkedSkillCollection(
        FormResolver $formResolver,
        Character $character,
    ): SkillLinkCollection {
        $buffer = [];

        $linkedSkills = $formResolver->getValue('linkedSkills', 'character');
        $linkedSkills = json_decode($linkedSkills, true, 512, JSON_THROW_ON_ERROR);

        foreach ($linkedSkills as $linkedSkill) {
            Assert::isArray($linkedSkill);
            Assert::string($linkedSkill['id']);
            Assert::integer($linkedSkill['points']);
            Assert::integer($linkedSkill['numberOfTimes']);
            Assert::boolean($linkedSkill['fastCasting']);
            Assert::boolean($linkedSkill['armouredCasting']);

            $buffer[] = SkillLink::create(
                $this->skillRepository->getById($linkedSkill['id']),
                $character,
                (int)$linkedSkill['points'],
                (int)$linkedSkill['numberOfTimes'],
                (bool)$linkedSkill['fastCasting'],
                (bool)$linkedSkill['armouredCasting'],
            );
        }

        return new SkillLinkCollection($buffer);
    }
}
