<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidValueException;

abstract readonly class Uuid
{
    private const string PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/';

    final public function __construct(public string $value)
    {
        if (1 !== preg_match(self::PATTERN, $value)) {
            throw new InvalidValueException(sprintf('"%s" is not a valid UUID.', $value));
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
