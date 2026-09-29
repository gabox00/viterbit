<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Exception;

use App\JobPosition\Domain\ValueObject\JobPositionHash;
use App\Shared\Domain\Exception\InvalidValueException;

final class UnknownJobPositionException extends InvalidValueException
{
    public static function withHash(JobPositionHash $jobPositionHash): self
    {
        return new self(sprintf('Job position "%s" does not exist.', $jobPositionHash->value));
    }
}
