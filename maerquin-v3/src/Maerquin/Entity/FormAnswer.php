<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\FormAnswer as FormAnswerModel;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'form_answer')]
#[ORM\UniqueConstraint(name: 'unique_submission_field', columns: ['submission_id', 'field_name'])]
class FormAnswer extends FormAnswerModel
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    protected UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: FormSubmission::class, inversedBy: 'answers')]
    #[ORM\JoinColumn(name: 'submission_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected FormSubmission $submission;

    #[ORM\Column(name: 'field_name', length: 128)]
    protected string $fieldName;

    #[ORM\Column(type: 'text')]
    protected string $value;
}
