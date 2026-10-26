<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\API;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface;
use SvenHK\Maerquin\Model\SkillCollection;
use SvenHK\Maerquin\Repository\SkillRepository;

class SkillsApiController extends Action
{
    public function __construct(
        private readonly SkillRepository $skillRepository,
    ) {
    }

    public function action(): ResponseInterface
    {
        return $this->respondWithData(
            new SkillCollection($this->skillRepository->findAllSorted())->serialize(false),
        );
    }
}
