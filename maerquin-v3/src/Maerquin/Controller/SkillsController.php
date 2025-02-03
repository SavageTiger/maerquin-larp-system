<?php

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Skill;
use SvenHK\Maerquin\Model\SkillCollection;
use SvenHK\Maerquin\Repository\SkillRepository;

class SkillsController extends Action
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
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'skills.html.twig',
            [
                'skills' => new SkillCollection($this->skillRepository->findAllSorted())->serialize()
            ]
        );
    }
}
