<?php

declare(strict_types=1);

namespace App\JobPosition\Infrastructure\Persistence\Type;

use App\JobPosition\Domain\ValueObject\JobPositionHash;
use App\Shared\Infrastructure\Persistence\Type\UuidType;

/** @extends UuidType<JobPositionHash> */
final class JobPositionHashType extends UuidType
{
    protected function valueObjectClass(): string
    {
        return JobPositionHash::class;
    }
}
