<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\API;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Override;
use Psr\Http\Message\ResponseInterface;
use SvenHK\Maerquin\Entity\Race;
use SvenHK\Maerquin\Entity\Skill;
use SvenHK\Maerquin\Model\Race as RaceModel;
use SvenHK\Maerquin\Model\SkillCollection;
use SvenHK\Maerquin\Repository\RaceRepository;
use SvenHK\Maerquin\Repository\SkillRepository;
use Webmozart\Assert\Assert;

class LinkableSkillsApiController extends Action
{
    /**
     * @var SkillRepository
     */
    private readonly EntityRepository $skillRepository;

    /**
     * @var RaceRepository
     */
    private readonly EntityRepository $raceRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->skillRepository = $entityManager->getRepository(Skill::class);
        $this->raceRepository = $entityManager->getRepository(Race::class);
    }

    #[Override]
    protected function action(): ResponseInterface
    {
        $buffer = [];

        $skills = new SkillCollection($this->skillRepository->findAllSorted());

        $race = $this->raceRepository->getById(
            $this->request->getQueryParams()['raceId'] ?? '',
        );

        Assert::isInstanceOf($race, RaceModel::class);

        foreach ($skills->getSkills() as $skill) {
            $buffer[] = $skill->serializeAsLinked(
                $race->getCustomPointsForSkill($skill),
            );
        }

        return $this->respondWithData(
            $buffer,
        );
    }
}
