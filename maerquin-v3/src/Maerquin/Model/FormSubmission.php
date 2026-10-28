<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Form;
use SvenHK\Maerquin\Entity\FormSubmission as FormSubmissionEntity;

class FormSubmission
{
    protected UuidInterface $id;
    protected Form $form;

    /**
     * @var Collection<int, FormAnswer>
     */
    protected Collection $answers;

    /**
     * @param array<string, string> $answers field name => value
     */
    public static function create(Form $form, array $answers = []): FormSubmissionEntity
    {
        $submission = new FormSubmissionEntity();
        $submission->id = Uuid::uuid4();
        $submission->form = $form;
        $submission->answers = new ArrayCollection();

        foreach ($answers as $fieldName => $value) {
            $submission->answers->add(FormAnswer::create($submission, (string)$fieldName, $value));
        }

        return $submission;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getAnswer(string $fieldName): null | string
    {
        return $this->getAnswers()[$fieldName] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public function getAnswers(): array
    {
        $answers = [];

        foreach ($this->answers as $answer) {
            $answers[$answer->getFieldName()] = $answer->getValue();
        }

        return $answers;
    }
}
