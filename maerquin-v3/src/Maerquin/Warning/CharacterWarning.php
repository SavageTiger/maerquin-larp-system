<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Warning;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Model\Character;
use SvenHK\Maerquin\Repository\RaceRepository;

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

    public function __construct(
        EntityManager $entityManager,
        Character $character,
    ) {
        $this->raceRepository = $entityManager->getRepository(Race::class);

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
            $this->checkSkillCosts($character),
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
    private function checkSkillCosts(Character $character): array
    {
        $warnings = [];

        $differentCostSkillLinks = $this->raceRepository->findDifferentPointSkillsSortedForRace(
            $character->getRace()->getId(),
        );

        foreach ($differentCostSkillLinks as $differentCostSkillLink) {
            $skill = $differentCostSkillLink->getSkill();

            $characterLinkedSkills = $character->getAllLinkedSkillsForSkill($skill);

            foreach ($characterLinkedSkills as $linkedSkill) {
                if ($linkedSkill->getPoints() > $differentCostSkillLink->getCustomPoints()) {
                    $warnings[] = sprintf(
                        'De skill "%s" heeft kosten "%s", maar kost voor een character met ras "%s" maximaal "%s".',
                        $skill->getName(),
                        $linkedSkill->getPoints(),
                        $character->getRace()->getName(),
                        $differentCostSkillLink->getCustomPoints(),
                    );
                }
            }
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
