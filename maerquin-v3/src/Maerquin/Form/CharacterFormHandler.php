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
    private readonly EntityRepository $characterRepository;

    /**
     * @var SkillRepository
     */
    private readonly EntityRepository $skillRepository;

    /**
     * @var PlayerRepository
     */
    private readonly EntityRepository $playerRepository;

    /**
     * @var RaceRepository
     */
    private readonly EntityRepository $raceRepository;

    /**
     * @var DeityRepository
     */
    private readonly EntityRepository $deityRepository;

    /**
     * @var CustomFieldRepository
     */
    private readonly EntityRepository $customFieldRepository;

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
        bool $isNewCharacter,
        Character $character,
        CustomFieldCollection $customFields,
        Request $request,
    ): void {
        $formResolver = FormResolver::createFromRequest($request);

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
                $character->getId(),
                $formResolver->getValue($customField->getId(), 'character'),
            );
        }

        $characterName = $formResolver->getValue('name', 'character');

        if (trim($characterName) === '') {
            $characterName = '(Naamloos)';
        }

        $character->updateCharacter(
            $characterName,
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
            (float)$formResolver->getValue('bonusXp', 'character', '0'),
            $isNewCharacter === true ?
                new SkillLinkCollection($character->getskills()) :
                $this->createLinkedSkillCollection($formResolver, $character),
        );

        $this->characterRepository->save($character);
    }

    private function createLinkedSkillCollection(
        FormResolver $formResolver,
        Character $character,
    ): SkillLinkCollection {
        $buffer = [];

        $linkedSkills = $formResolver->getValue('linkedSkills', 'character', 'invalid');
        $linkedSkills = json_decode($linkedSkills, true, 512, JSON_THROW_ON_ERROR);

        foreach ($linkedSkills as $linkedSkill) {
            Assert::isArray($linkedSkill);
            Assert::string($linkedSkill['id']);
            Assert::numeric($linkedSkill['points']);
            Assert::integer($linkedSkill['numberOfTimes']);
            Assert::boolean($linkedSkill['fastCasting']);
            Assert::boolean($linkedSkill['armouredCasting']);

            $buffer[] = SkillLink::create(
                $this->skillRepository->getById($linkedSkill['id']),
                $character,
                (float)$linkedSkill['points'],
                (int)$linkedSkill['numberOfTimes'],
                (bool)$linkedSkill['fastCasting'],
                (bool)$linkedSkill['armouredCasting'],
            );
        }

        return new SkillLinkCollection($buffer);
    }
}
