<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Warning;

use SvenHK\Maerquin\Model\Character;
use SvenHK\Maerquin\Repository\EventRepository;
use SvenHK\Maerquin\Repository\RaceRepository;
use SvenHK\Maerquin\Repository\SkillRepository;
use SvenHK\Maerquin\Translation\Translator;

final readonly class CharacterWarning
{
    public function __construct(
        private RaceRepository $raceRepository,
        private SkillRepository $skillRepository,
        private EventRepository $eventRepository,
        private Translator $translator,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function getWarnings(Character $character): array
    {
        return $this->analyzeCharacter($character);
    }

    /**
     * @return array<int, string>
     */
    private function analyzeCharacter(Character $character): array
    {
        return array_merge(
            $this->checkForbiddenSkills($character),
            $this->checkMandatorySkills($character),
            $this->checkSkillRequirements($character),
            $this->checkExceedsPoints($character),
            $this->checkExceedsBoughtSkills($character),
        );
    }

    /**
     * @return array<int, string>
     */
    private function checkForbiddenSkills(Character $character): array
    {
        $warnings = [];

        $forbiddenSkillLinks = $this->raceRepository->findAllForbiddenSortedForRace(
            $character->getRace()->getId(),
        );

        foreach ($forbiddenSkillLinks as $forbiddenSkillLink) {
            $skill = $forbiddenSkillLink->getSkill();

            if ($character->hasSkill($skill) === true) {
                $warnings[] = $this->translator->trans('character.warning.forbidden_skill', [
                    'skill' => $skill->getName(),
                    'race' => $character->getRace()->getName(),
                ]);
            }
        }

        return $warnings;
    }

    /**
     * @return array<int, string>
     */
    private function checkMandatorySkills(Character $character): array
    {
        $warnings = [];

        $forbiddenSkillLinks = $this->raceRepository->findAllMandatorySortedForRace(
            $character->getRace()->getId(),
        );

        foreach ($forbiddenSkillLinks as $forbiddenSkillLink) {
            $skill = $forbiddenSkillLink->getSkill();

            if ($character->hasSkill($skill) === false) {
                $warnings[] = $this->translator->trans('character.warning.mandatory_skill', [
                    'skill' => $skill->getName(),
                    'race' => $character->getRace()->getName(),
                ]);
            }
        }

        return $warnings;
    }

    /**
     * @return array<int, string>
     */
    private function checkSkillRequirements(Character $character): array
    {
        $warnings = [];

        foreach ($character->getSkills() as $linkedSkill) {
            $requiredParentSkillIds = [
                $linkedSkill->getSkill()->getParentRequirementSkillId(),
                $linkedSkill->getSkill()->getSecondaryParentRequirementSkillId(),
            ];

            foreach ($requiredParentSkillIds as $requiredParentSkillId) {
                if ($requiredParentSkillId === null) {
                    continue;
                }

                if ($character->hasSkill($requiredParentSkillId) === false) {
                    $warnings[] = $this->translator->trans('character.warning.missing_required_skill', [
                        'skill' => $linkedSkill->getSkill()->getName(),
                        'requiredSkill' => $this->skillRepository->getById($requiredParentSkillId)->getName(),
                    ]);
                }
            }
        }

        return $warnings;
    }

    /**
     * @return array<int, string>
     */
    private function checkExceedsPoints(Character $character): array
    {
        $warnings = [];

        $eventsForCharacter = $this->eventRepository->findAllForCharacter($character->getId());

        $totalPoints = Character::BASE_XP;
        $totalPoints += $character->getBonusXp();

        foreach ($eventsForCharacter as $presentAtEvent) {
            $totalPoints += $presentAtEvent->getPoints();
        }

        if ($totalPoints > Character::MAX_XP) {
            $totalPoints = Character::MAX_XP;
        }

        if ($character->spendPoints() > $totalPoints) {
            $warnings[] = $this->translator->trans('character.warning.not_enough_points', [
                'available' => $totalPoints,
                'required' => $character->spendPoints(),
            ]);
        }

        return $warnings;
    }

    /**
     * @return array<int, string>
     */
    private function checkExceedsBoughtSkills(Character $character): array
    {
        $warnings = [];

        $boughtSkillCount = [];
        $allowedAmount = [];

        foreach ($character->getSkills() as $skillLink) {
            $skillId = $skillLink->getSkill()->getId();

            if (isset($boughtSkillCount[$skillId]) === false) {
                $boughtSkillCount[$skillId] = 0;

                $allowedAmount[$skillId] =
                    $skillLink->getSkill()->getMaximumAmountBuyable();
            }

            $boughtSkillCount[$skillId] += $skillLink->getNumberOfTimes();
        }

        foreach ($boughtSkillCount as $skillId => $amountBought) {
            if ($amountBought > $allowedAmount[$skillId]) {
                $warnings[] = $this->translator->trans('character.warning.skill_bought_too_often', [
                    'skill' => $this->skillRepository->getById($skillId)->getName(),
                    'amount' => $amountBought,
                    'maximum' => $allowedAmount[$skillId],
                ]);
            }
        }

        return $warnings;
    }
}
