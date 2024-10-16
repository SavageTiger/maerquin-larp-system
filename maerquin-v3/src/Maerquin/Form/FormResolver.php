<?php

namespace SvenHK\Maerquin\Form;

use Psr\Http\Message\ServerRequestInterface as Request;

class FormResolver
{
    private function __construct(private Request $request)
    {
    }

    public static function createFromRequest(Request $request): self
    {
        return new self($request);
    }

    /**
     * @throws MissingFormFieldException
     */
    public function getValue(string $name, string|null $default = null): string
    {
        $value = $this->request->getParsedBody()[$name] ?? $default;

        if (is_string($value) === false) {
            throw new MissingFormFieldException(sprintf('%s is not a valid form element', $name));
        }

        return $value;
    }
}
