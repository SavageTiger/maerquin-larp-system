<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\API;

use App\Application\Actions\Action;
use Override;
use Psr\Http\Message\ResponseInterface;
use SvenHK\Maerquin\Model\Race as RaceModel;
use SvenHK\Maerquin\Model\SkillCollection;
use SvenHK\Maerquin\Repository\RaceRepository;
use SvenHK\Maerquin\Repository\SkillRepository;
use Webmozart\Assert\Assert;

class LinkableSkillsApiController extends Action
{
    public function __construct(
        private readonly SkillRepository $skillRepository,
        private readonly RaceRepository $raceRepository,
    ) {
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
