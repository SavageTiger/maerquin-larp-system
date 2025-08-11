<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Deity;
use SvenHK\Maerquin\Entity\Element;
use SvenHK\Maerquin\Entity\Skill;
use SvenHK\Maerquin\Entity\SkillType;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Model\SkillSkillLink;
use SvenHK\Maerquin\Repository\DeityRepository;
use SvenHK\Maerquin\Repository\ElementRepository;
use SvenHK\Maerquin\Repository\SkillRepository;
use SvenHK\Maerquin\Repository\SkillTypeRepository;

class SkillFormHandler
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

    public function __construct(EntityManager $entityManager)
    {
        $this->skillRepository = $entityManager->getRepository(Skill::class);
        $this->skillTypeRepository = $entityManager->getRepository(SkillType::class);
        $this->deityRepository = $entityManager->getRepository(Deity::class);
        $this->elementRepository = $entityManager->getRepository(Element::class);
    }

    /**
     * @throws MissingFormFieldException
     * @throws MaerquinEntityNotFoundException
     */
    public function handle(string $skillId, Request $request): void
    {
        $formResolver = FormResolver::createFromRequest($request);

        $skill = $this->skillRepository->getById($skillId);

        $requiredSkillLink = null;
        $requiredSkill = $this->skillRepository->findById(
            $formResolver->getValue('requiredSkillId', 'skill'),
        );

        if ($requiredSkill !== null) {
            $requiredSkillLink = SkillSkillLink::createForSkill($skill, $requiredSkill);
        }

        $skill->updateSkill(
            $formResolver->getValue('name', 'skill'),
            $this->deityRepository->findById($formResolver->getValue('deityElementId', 'skill')),
            $this->elementRepository->findById($formResolver->getValue('deityElementId', 'skill')),
            $this->skillTypeRepository->getOneById($formResolver->getValue('skillTypeId', 'skill')),
            $requiredSkillLink,
            (int)$formResolver->getValue('points', 'skill'),
            (int)$formResolver->getValue('maximumAmountBuyable', 'skill'),
            (int)$formResolver->getValue('level', 'skill'),
            $formResolver->getValue('distance', 'skill'),
            $formResolver->getValue('duration', 'skill'),
            $formResolver->getBoolean('isNotFreelyAvailable', 'skill'),
            $formResolver->getBoolean('isHidden', 'skill'),
            $formResolver->getValue('description', 'skill'),
            $formResolver->getValue('remarks', 'skill'),
        );

        $this->skillRepository->save($skill);
    }
}
