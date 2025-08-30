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
use SvenHK\Maerquin\Model\RequirementType;
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
    private readonly EntityRepository $skillRepository;

    /**
     * @var SkillTypeRepository
     */
    private readonly EntityRepository $skillTypeRepository;

    /**
     * @var DeityRepository
     */
    private readonly EntityRepository $deityRepository;

    /**
     * @var ElementRepository
     */
    private readonly EntityRepository $elementRepository;

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
    public function handle(Skill $skill, Request $request): void
    {
        $formResolver = FormResolver::createFromRequest($request);

        $primaryRequiredSkillLink = $this->getRequiredSkillLink(true, $formResolver, $skill);
        $secondaryRequiredSkillLink = $this->getRequiredSkillLink(false, $formResolver, $skill);

        $name = $formResolver->getValue('name', 'skill');

        if (trim($name) === '') {
            $name = '(Naamloos)';
        }

        $skill->updateSkill(
            $name,
            $this->deityRepository->findById($formResolver->getValue('deityElementId', 'skill')),
            $this->elementRepository->findById($formResolver->getValue('deityElementId', 'skill')),
            $this->skillTypeRepository->getOneById($formResolver->getValue('skillTypeId', 'skill')),
            $primaryRequiredSkillLink,
            $secondaryRequiredSkillLink,
            (int)$formResolver->getValue('points', 'skill'),
            (int)$formResolver->getValue('maximumAmountBuyable', 'skill'),
            (int)$formResolver->getValue('level', 'skill'),
            $formResolver->getValue('distance', 'skill'),
            $formResolver->getValue('duration', 'skill'),
            $formResolver->getBoolean('isNotFreelyAvailable', 'skill'),
            $formResolver->getBoolean('isHidden', 'skill'),
            $formResolver->getValue('description', 'skill'),
            $formResolver->getValue('remarks', 'skill'),
            $formResolver->getBoolean('hasFastCasting', 'skill'),
            $formResolver->getBoolean('hasArmorCasting', 'skill'),
        );

        $this->skillRepository->save($skill);
    }

    /**
     * @throws MissingFormFieldException
     */
    public function getRequiredSkillLink(bool $primary, FormResolver $formResolver, Skill $skill): null | SkillSkillLink
    {
        $requiredSkill = $this->skillRepository->findById(
            $formResolver->getValue(
                $primary === true ? 'requiredSkillIdPrimary' : 'requiredSkillIdSecondary',
                'skill',
            ),
        );

        if ($requiredSkill === null) {
            return null;
        }

        return SkillSkillLink::createForSkill(
            $skill,
            $requiredSkill,
            $primary === true ? RequirementType::PrimarySkill : RequirementType::SecondarySkill,
        );
    }
}
