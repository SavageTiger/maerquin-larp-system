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
        private readonly SkillFormHandler $skillFormHandler,
        EntityManager $entityManager,
    ) {
        $this->skillRepository = $entityManager->getRepository(Skill::class);
        $this->skillTypeRepository = $entityManager->getRepository(SkillType::class);
        $this->deityRepository = $entityManager->getRepository(Deity::class);
        $this->elementRepository = $entityManager->getRepository(Element::class);
    }

    public function action(): ResponseInterface
    {
        $skillId = (string)($this->request->getAttribute('skillId') ?? '');

        if (str_contains($this->request->getUri()->getPath(), 'create.html') === true) {
            $skillId = Uuid::uuid4()->toString();
        }

        if ($this->request->getMethod() === 'POST' && Uuid::isValid($skillId) === true) {
            $this->skillFormHandler->handle(
                $this->getSkill(Uuid::fromString($skillId)),
                $this->request,
            );
        }

        if ($skillId !== '' && Uuid::isValid($skillId) === true) {
            return $this->renderEditView(Uuid::fromString($skillId));
        }

        return $this->renderListView();
    }

    private function getSkill(UuidInterface $skillId): Skill
    {
        $skill = $this->skillRepository->findById($skillId->toString());

        if ($skill === null) {
            $skill = Skill::create(
                $skillId,
                $this->skillTypeRepository->getDefault(),
            );
        }

        return $skill;
    }

    private function renderEditView(UuidInterface $skillId): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'skill.html.twig',
            array_merge(
                $this->getBaseSkillsContext(),
                [
                    'skill' => $this->getSkill($skillId),
                    'deities' => $this->deityRepository->findAllSorted(),
                    'elements' => $this->elementRepository->findAllSorted(),
                    'persisted' => str_contains($this->request->getUri()->getPath(), '/persisted/'),
                ],
            ),
        );
    }

    /**
     * @return array{
     *     skills: SkillCollection,
     *     skillTypes: SkillTypeCollection
     * }
     */
    private function getBaseSkillsContext(): array
    {
        return [
            'skills' => new SkillCollection($this->skillRepository->findAllSorted())->serialize(true),
            'skillTypes' => new SkillTypeCollection($this->skillTypeRepository->findAllSorted()),
        ];
    }

    private function renderListView(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'skills.html.twig',
            $this->getBaseSkillsContext(),
        );
    }
}
