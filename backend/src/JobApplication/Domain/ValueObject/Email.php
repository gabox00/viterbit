<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidValueException;

final readonly class Email
{
    public string $value;

    public function __construct(string $value)
    {
        $normalizedValue = mb_strtolower(trim($value));
        if (false === filter_var($normalizedValue, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidValueException(sprintf('"%s" is not a valid email.', $value));
        }

        $this->value = $normalizedValue;
    }
}
