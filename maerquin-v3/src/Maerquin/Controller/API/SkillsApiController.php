<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\API;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use SvenHK\Maerquin\Entity\Skill;
use SvenHK\Maerquin\Model\SkillCollection;
use SvenHK\Maerquin\Repository\SkillRepository;

class SkillsApiController extends Action
{
    /**
     * @var SkillRepository
     */
    private EntityRepository $skillRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->skillRepository = $entityManager->getRepository(Skill::class);
    }

    public function action(): ResponseInterface
    {
        return $this->respondWithData(
            new SkillCollection($this->skillRepository->findAllSorted())->serialize(false),
        );
    }
}
