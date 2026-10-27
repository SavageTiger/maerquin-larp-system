<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\FormSubmission as FormSubmissionModel;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'form_submission')]
class FormSubmission extends FormSubmissionModel
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    protected UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Form::class)]
    #[ORM\JoinColumn(name: 'form_id', referencedColumnName: 'id', nullable: false)]
    protected Form $form;

    #[ORM\OneToMany(
        targetEntity: FormAnswer::class,
        mappedBy: 'submission',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    protected Collection $answers;
}
