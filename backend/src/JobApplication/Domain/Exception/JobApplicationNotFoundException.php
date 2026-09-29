<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Exception;

use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\Shared\Domain\Exception\NotFoundException;

final class JobApplicationNotFoundException extends NotFoundException
{
    public static function withHash(JobApplicationHash $hash): self
    {
        return new self(sprintf('Job application "%s" not found.', $hash->value));
    }
}
