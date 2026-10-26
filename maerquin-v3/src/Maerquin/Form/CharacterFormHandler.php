<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Character;
use SvenHK\Maerquin\Model\CustomFieldCollection;
use SvenHK\Maerquin\Model\SkillLink;
use SvenHK\Maerquin\Model\SkillLinkCollection;
use SvenHK\Maerquin\Repository\CustomFieldRepository;
use SvenHK\Maerquin\Repository\DeityRepository;
use SvenHK\Maerquin\Repository\PlayerRepository;
use SvenHK\Maerquin\Repository\RaceRepository;
use SvenHK\Maerquin\Repository\SkillRepository;
use SvenHK\Maerquin\Warning\CharacterWarningUpdater;
use Webmozart\Assert\Assert;

class CharacterFormHandler
{
    public function __construct(
        private readonly SkillRepository $skillRepository,
        private readonly PlayerRepository $playerRepository,
        private readonly RaceRepository $raceRepository,
        private readonly DeityRepository $deityRepository,
        private readonly CustomFieldRepository $customFieldRepository,
        private readonly CharacterWarningUpdater $characterWarningUpdater,
    ) {
    }

    public function handle(
        bool $isNewCharacter,
        Character $character,
        CustomFieldCollection $customFields,
        Request $request,
    ): void {
        $formResolver = FormResolver::createFromRequest($request);

        $player = $this->playerRepository->findById(
            $formResolver->getValue('playerId', 'character'),
        );

        $race = $this->raceRepository->getById(
            $formResolver->getValue('raceId', 'character'),
        );

        $primaryDeity = $this->deityRepository->findById(
            $formResolver->getValue('primaryDeityId', 'character'),
        );

        $secondaryDeity = $this->deityRepository->findById(
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

        $this->characterWarningUpdater->updateAndSave($character);
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
