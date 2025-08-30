<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

enum RequirementType: string
{
    case PrimarySkill = 'PRIMARY_SKILL';
    case SecondarySkill = 'SECONDARY_SKILL';
}
