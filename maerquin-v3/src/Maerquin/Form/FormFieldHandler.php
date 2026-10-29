<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Entity\Form;
use SvenHK\Maerquin\Entity\FormSection;
use SvenHK\Maerquin\Exception\Form\FormFieldException;
use SvenHK\Maerquin\Exception\Request\MissingFormFieldException;
use SvenHK\Maerquin\Model\FormFieldType;
use SvenHK\Maerquin\Repository\FormRepository;

readonly class FormFieldHandler
{
    public function __construct(
        private FormRepository $formRepository,
    ) {
    }

    /**
     * @throws MissingFormFieldException
     * @throws FormFieldException
     */
    public function replace(Form $form, FormSection $section, Request $request): void
    {
        $rows = [];

        foreach (FormResolver::createFromRequest($request)->getArray('rows', 'section') as $fields) {
            if (is_array($fields) === false) {
                throw FormFieldException::layoutInvalid();
            }

            if ($fields !== []) {
                $rows[] = array_map($this->resolveField(...), array_values($fields));
            }
        }

        $form->replaceSectionFields($section, $rows);

        $this->formRepository->save($form);
    }

    /**
     *
     * @throws FormFieldException
     *
     * @return array{id: UuidInterface, label: string, type: FormFieldType, required: bool}
     */
    private function resolveField(mixed $field): array
    {
        if (
            is_array($field) === false
            || is_string($field['id'] ?? null) === false
            || Uuid::isValid($field['id']) === false
            || is_string($field['label'] ?? null) === false
            || is_string($field['type'] ?? null) === false
            || is_bool($field['required'] ?? null) === false
        ) {
            throw FormFieldException::layoutInvalid();
        }

        return [
            'id' => Uuid::fromString($field['id']),
            'label' => trim($field['label']),
            'type' => FormFieldType::tryFrom($field['type']) ?? throw FormFieldException::typeInvalid($field['type']),
            'required' => $field['required'],
        ];
    }
}
