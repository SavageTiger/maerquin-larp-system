<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use JsonException;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Entity\RaceSkillLink;
use SvenHK\Maerquin\Entity\Skill;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Repository\RaceRepository;
use SvenHK\Maerquin\Repository\SkillRepository;

class RaceFormHandler
{
    /**
     * @var RaceRepository
     */
    private EntityRepository $raceRepository;

    /**
     * @var SkillRepository
     */
    private EntityRepository $skillRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->raceRepository = $entityManager->getRepository(Race::class);
        $this->skillRepository = $entityManager->getRepository(Skill::class);
    }

    /**
     * @throws MissingFormFieldException
     * @throws MaerquinEntityNotFoundException
     * @throws JsonException
     */
    public function handle(Race $race, Request $request): void
    {
        $formResolver = FormResolver::createFromRequest($request);

        $skillConnections = [];

        $mandatorySkills = json_decode($formResolver->getValue('mandatorySkills', 'race'), true, 512, JSON_THROW_ON_ERROR);
        $forbiddenSkills = json_decode($formResolver->getValue('forbiddenSkills', 'race'), true, 512, JSON_THROW_ON_ERROR);
        $differentPointSkills = json_decode($formResolver->getValue('differentPointSkills', 'race'), true, 512, JSON_THROW_ON_ERROR);

        foreach ($mandatorySkills['skillIds'] ?? [] as $skillId) {
            $skillConnections[] = RaceSkillLink::createMandatory(
                $race,
                $this->skillRepository->getById($skillId),
            );
        }

        foreach ($forbiddenSkills['skillIds'] ?? [] as $skillId) {
            $skillConnections[] = RaceSkillLink::createForbidden(
                $race,
                $this->skillRepository->getById($skillId),
            );
        }

        foreach ($differentPointSkills['skillIds'] ?? [] as $index => $skillId) {
            $customPoints = $differentPointSkills['points'][$index] ?? 0;

            $skillConnections[] = RaceSkillLink::createWithCustomPoints(
                $race,
                $this->skillRepository->getById($skillId),
                $customPoints,
            );
        }

        $name = $formResolver->getValue('name', 'race');

        if (trim($name) === '') {
            $name = '(Naamloos)';
        }

        $race->updateRace(
            $name,
            $skillConnections,
        );

        $this->raceRepository->save($race);
    }
}
