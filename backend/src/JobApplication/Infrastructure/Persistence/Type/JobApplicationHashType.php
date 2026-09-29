<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Persistence\Type;

use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\Shared\Infrastructure\Persistence\Type\UuidType;

/** @extends UuidType<JobApplicationHash> */
final class JobApplicationHashType extends UuidType
{
    protected function valueObjectClass(): string
    {
        return JobApplicationHash::class;
    }
}
