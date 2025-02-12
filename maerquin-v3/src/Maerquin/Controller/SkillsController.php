<?php

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Deity;
use SvenHK\Maerquin\Entity\Element;
use SvenHK\Maerquin\Entity\Skill;
use SvenHK\Maerquin\Entity\SkillType;
use SvenHK\Maerquin\Form\SkillFormHandler;
use SvenHK\Maerquin\Model\SkillCollection;
use SvenHK\Maerquin\Model\SkillTypeCollection;
use SvenHK\Maerquin\Repository\DeityRepository;
use SvenHK\Maerquin\Repository\ElementRepository;
use SvenHK\Maerquin\Repository\SkillRepository;
use SvenHK\Maerquin\Repository\SkillTypeRepository;

class SkillsController extends Action
{
    /**
     * @var SkillRepository
     */
    private EntityRepository $skillRepository;

    /**
     * @var SkillTypeRepository
     */
    private EntityRepository $skillTypeRepository;

    /**
     * @var DeityRepository
     */
    private EntityRepository $deityRepository;

    /**
     * @var ElementRepository
     */
    private EntityRepository $elementRepository;

    public function __construct(
        readonly private SkillFormHandler $skillFormHandler,
        EntityManager $entityManager
    ) {
        $this->skillRepository = $entityManager->getRepository(Skill::class);
        $this->skillTypeRepository = $entityManager->getRepository(SkillType::class);
        $this->deityRepository = $entityManager->getRepository(Deity::class);
        $this->elementRepository = $entityManager->getRepository(Element::class);
    }

    public function action() : ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $skillId = (string)($this->request->getAttribute('skillId') ?? '');

        if ($this->request->getMethod() === 'POST' && Uuid::isValid($skillId)) {
            $this->skillFormHandler->handle($skillId, $this->request);
        }

        $viewContext = [
            'skills' => new SkillCollection($this->skillRepository->findAllSorted())->serialize(true),
            'skillTypes' => new SkillTypeCollection($this->skillTypeRepository->findAllSorted()),
        ];

        if ($skillId !== '') {
            return $view->render(
                $this->response,
                'skill.html.twig',
                array_merge($viewContext,
                    [
                        'skill' => $this->skillRepository->getById($skillId),
                        'deities' => $this->deityRepository->findAllSorted(),
                        'elements' => $this->elementRepository->findAllSorted()
                    ]
                )
            );
        }

        return $view->render(
            $this->response,
            'skills.html.twig',
            $viewContext
        );
    }
}
