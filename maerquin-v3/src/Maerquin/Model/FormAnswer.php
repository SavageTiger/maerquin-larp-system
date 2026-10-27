<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\FormAnswer as FormAnswerEntity;
use SvenHK\Maerquin\Entity\FormSubmission;

class FormAnswer
{
    protected UuidInterface $id;
    protected FormSubmission $submission;
    protected string $fieldName;
    protected string $value;

    public static function create(FormSubmission $submission, string $fieldName, string $value): FormAnswerEntity
    {
        $answer = new FormAnswerEntity();
        $answer->id = Uuid::uuid4();
        $answer->submission = $submission;
        $answer->fieldName = $fieldName;
        $answer->value = $value;

        return $answer;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
