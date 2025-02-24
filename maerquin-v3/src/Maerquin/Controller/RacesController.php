<?php

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Entity\Skill;
use SvenHK\Maerquin\Model\RaceCollection;
use SvenHK\Maerquin\Model\SkillRaceConnectionCollection;
use SvenHK\Maerquin\Repository\RaceRepository;
use SvenHK\Maerquin\Repository\SkillRepository;

class RacesController extends Action
{
    /**
     * @var RaceRepository
     */
    private EntityRepository $raceRepository;

    /**
     * @var SkillRepository
     */
    private EntityRepository $skillRepository;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->raceRepository = $entityManager->getRepository(Race::class);
        $this->skillRepository = $entityManager->getRepository(Skill::class);
    }

    public function action() : ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $raceId = (string)($this->request->getAttribute('raceId') ?? '');

        if ($this->request->getMethod() === 'POST' && Uuid::isValid($raceId)) {
            // FORM HANDLE
        }

        if ($raceId !== '' && Uuid::isValid($raceId)) {
            return $view->render(
                $this->response,
                'race.html.twig',
                [
                    'race' => $this->raceRepository->getById($raceId),
                    'mandatorySkills' => new SkillRaceConnectionCollection(
                        $this->skillRepository->findAllMandatorySortedForRace($raceId)
                    ),
                    'forbiddenSkills' => new SkillRaceConnectionCollection(
                        $this->skillRepository->findAllForbiddenSortedForRace($raceId)
                    ),
                    'differentPointSkills' => new SkillRaceConnectionCollection(
                        $this->skillRepository->findDifferentPointSkillsSortedForRace($raceId)
                    ),
                ]
            );
        }

        return $view->render(
            $this->response,
            'races.html.twig', [
                'races' => new RaceCollection($this->raceRepository->findAllSorted()),
            ]
        );
    }
}
