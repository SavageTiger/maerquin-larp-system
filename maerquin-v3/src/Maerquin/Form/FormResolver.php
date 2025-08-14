<?php

declare(strict_types=1);

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
    public function getValue(string $name, null | string $namespace = null, null | string $default = null): string
    {
        $value = is_string($namespace) === false ?
            $this->request->getParsedBody()[$name] ?? $default :
            $this->request->getParsedBody()[$namespace][$name] ?? $default;

        if (is_string($value) === false) {
            throw new MissingFormFieldException(sprintf('%s is not a valid form element', $name));
        }

        return $value;
    }

    /**
     * @throws MissingFormFieldException
     */
    public function getBoolean(string $name, null | string $namespace = null, null | bool $default = null): bool
    {
        $value = is_string($namespace) === false ?
            $this->request->getParsedBody()[$name] ?? $default :
            $this->request->getParsedBody()[$namespace][$name] ?? $default;

        if (is_string($value)) {
            $value = $value === 'true';
        }

        if (is_bool($value) === false) {
            throw new MissingFormFieldException(sprintf('%s is not a valid form element', $name));
        }

        return $value;
    }
}
