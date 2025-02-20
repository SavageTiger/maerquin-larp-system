<?php

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Model\RaceCollection;
use SvenHK\Maerquin\Repository\RaceRepository;

class RacesController extends Action
{
    /**
     * @var RaceRepository
     */
    private EntityRepository $raceRepository;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->raceRepository = $entityManager->getRepository(Race::class);
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
                ['race' => $this->raceRepository->getById($raceId)]
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
