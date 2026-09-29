<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidValueException;

final readonly class AiScore
{
    private const int MIN = 0;
    private const int MAX = 100;

    public function __construct(public int $value)
    {
        if ($value < self::MIN || $value > self::MAX) {
            throw new InvalidValueException(sprintf('AI score must be between %d and %d, got %d.', self::MIN, self::MAX, $value));
        }
    }
}
