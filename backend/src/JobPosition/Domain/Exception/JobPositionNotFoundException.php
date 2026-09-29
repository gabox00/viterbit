<?php

declare(strict_types=1);

namespace App\JobPosition\Domain\Exception;

use App\JobPosition\Domain\ValueObject\JobPositionHash;
use App\Shared\Domain\Exception\NotFoundException;

final class JobPositionNotFoundException extends NotFoundException
{
    public static function withHash(JobPositionHash $hash): self
    {
        return new self(sprintf('Job position "%s" not found.', $hash->value));
    }
}
