<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Form\RaceFormHandler;
use SvenHK\Maerquin\Model\Race as RaceModel;
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
        $raceId = (string)($this->request->getAttribute('raceId') ?? '');

        if (str_contains($this->request->getUri()->getPath(), 'create.html') === true) {
            $raceId = Uuid::uuid4()->toString();
        }

        if ($this->request->getMethod() === 'POST' && Uuid::isValid($raceId)) {
            $this->raceFormHandler->handle(
                $this->getRace(Uuid::fromString($raceId)),
                $this->request,
            );
        }

        if (Uuid::isValid($raceId) === true) {
            return $this->renderEditView(Uuid::fromString($raceId));
        }

        return $this->renderListView();
    }

    private function getRace(UuidInterface $raceId): Race
    {
        $race = $this->raceRepository->findById($raceId->toString());

        if ($race === null) {
            $race = RaceModel::create(
                raceId: $raceId,
            );
        }

        return $race;
    }

    private function renderEditView(UuidInterface $raceId): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'race.html.twig',
            [
                'race' => $this->getRace($raceId),
                'mandatorySkills' => new SkillRaceConnectionCollection(
                    $this->raceRepository->findAllMandatorySortedForRace($raceId->toString()),
                ),
                'forbiddenSkills' => new SkillRaceConnectionCollection(
                    $this->raceRepository->findAllForbiddenSortedForRace($raceId->toString()),
                ),
                'differentPointSkills' => new SkillRaceConnectionCollection(
                    $this->raceRepository->findDifferentPointSkillsSortedForRace($raceId->toString()),
                ),
            ],
        );
    }

    public function renderListView(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'races.html.twig',
            [
                'races' => new RaceCollection($this->raceRepository->findAllSorted()),
            ],
        );
    }
}
