<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Warning;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Entity\Skill;
use SvenHK\Maerquin\Model\Character;
use SvenHK\Maerquin\Repository\EventRepository;
use SvenHK\Maerquin\Repository\RaceRepository;
use SvenHK\Maerquin\Repository\SkillRepository;

final readonly class CharacterWarning
{
    /**
     * @var array<int, string>
     */
    private array $warnings;

    /**
     * @var RaceRepository
     */
    private EntityRepository $raceRepository;

    /**
     * @var SkillRepository
     */
    private EntityRepository $skillRepository;

    /**
     * @var EventRepository
     */
    private EntityRepository $eventRepository;

    public function __construct(
        EntityManager $entityManager,
        Character $character,
    ) {
        $this->raceRepository = $entityManager->getRepository(Race::class);
        $this->skillRepository = $entityManager->getRepository(Skill::class);
        $this->eventRepository = $entityManager->getRepository(Event::class);

        $this->warnings = $this->analyzeCharacter($character);
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
                $warnings[] = sprintf(
                    'De skill "%s" is niet toegestaan voor een character met ras "%s".',
                    $skill->getName(),
                    $character->getRace()->getName(),
                );
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
                $warnings[] = sprintf(
                    'De skill "%s" is verplicht voor een character met ras "%s".',
                    $skill->getName(),
                    $character->getRace()->getName(),
                );
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
            $requiredParentSkillId = $linkedSkill->getSkill()->getParentRequirementSkillId();

            if ($requiredParentSkillId === null) {
                continue;
            }

            if ($character->hasSkill($requiredParentSkillId) === false) {
                $warnings[] = sprintf(
                    'De skill "%s" vereist eerst "%s", maar die ontbreekt nog.',
                    $linkedSkill->getSkill()->getName(),
                    $this->skillRepository->getById($requiredParentSkillId)->getName(),
                );
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

        foreach ($eventsForCharacter as $presentAtEvent) {
            $totalPoints += $presentAtEvent->getPoints();
        }

        if ($totalPoints > Character::MAX_XP) {
            $totalPoints = Character::MAX_XP;
        }

        if ($character->spendPoints() > $totalPoints) {
            $warnings[] = sprintf(
                'Het karakter heeft niet genoeg punten: %s spendeerbaar, %s vereist.',
                $totalPoints,
                $character->spendPoints(),
            );
        }

        return $warnings;
    }

    /**
     * @return array<int, string>
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }
}
