<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Form\RaceFormHandler;
use SvenHK\Maerquin\Model\RaceCollection;
use SvenHK\Maerquin\Model\SkillRaceConnectionCollection;
use SvenHK\Maerquin\Repository\RaceRepository;

class RacesController extends Action
{
    /**
     * @var RaceRepository
     */
    private EntityRepository $raceRepository;

    public function __construct(
        private readonly RaceFormHandler $raceFormHandler,
        EntityManager $entityManager,
    ) {
        $this->raceRepository = $entityManager->getRepository(Race::class);
    }

    public function action(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $raceId = (string)($this->request->getAttribute('raceId') ?? '');

        if ($this->request->getMethod() === 'POST' && Uuid::isValid($raceId)) {
            $this->raceFormHandler->handle($raceId, $this->request);
        }

        if ($raceId !== '' && Uuid::isValid($raceId)) {
            return $view->render(
                $this->response,
                'race.html.twig',
                [
                    'race' => $this->raceRepository->getById($raceId),
                    'mandatorySkills' => new SkillRaceConnectionCollection(
                        $this->raceRepository->findAllMandatorySortedForRace($raceId),
                    ),
                    'forbiddenSkills' => new SkillRaceConnectionCollection(
                        $this->raceRepository->findAllForbiddenSortedForRace($raceId),
                    ),
                    'differentPointSkills' => new SkillRaceConnectionCollection(
                        $this->raceRepository->findDifferentPointSkillsSortedForRace($raceId),
                    ),
                ],
            );
        }

        return $view->render(
            $this->response,
            'races.html.twig',
            [
                'races' => new RaceCollection($this->raceRepository->findAllSorted()),
            ],
        );
    }
}
