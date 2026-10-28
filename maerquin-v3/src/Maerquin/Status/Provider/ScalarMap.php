<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider;

/**
 * Typed access to the loosely typed arrays that DBAL, Predis and json_decode() return.
 *
 * Only scalar values are kept; everything is validated once here instead of cast at every call site.
 */
final readonly class ScalarMap
{
    /**
     * @param array<string, string|int|float|bool> $values
     */
    private function __construct(
        private array $values,
    ) {
    }

    public static function fromArray(mixed $values): self
    {
        if (is_array($values) === false) {
            return new self([]);
        }

        $scalars = [];

        foreach ($values as $key => $value) {
            if (is_scalar($value)) {
                $scalars[(string)$key] = $value;
            }
        }

        return new self($scalars);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    public function getInt(string $key): int
    {
        $value = $this->values[$key] ?? 0;

        return is_numeric($value) ? (int)$value : 0;
    }

    public function getString(string $key): string
    {
        $value = $this->values[$key] ?? '';

        return is_bool($value) ? ($value ? '1' : '0') : (string)$value;
    }

    /**
     * @return list<string>
     */
    public function keys(): array
    {
        return array_map(strval(...), array_keys($this->values));
    }
}
