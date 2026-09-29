<?php

declare(strict_types=1);

namespace App\Tests\Mother;

use App\JobPosition\Domain\Entity\JobPosition;
use App\JobPosition\Domain\ValueObject\JobPositionHash;

final class JobPositionMother
{
    /** An unmanaged copy of the seeded backend position, for tests that do not touch the database. */
    public static function backend(): JobPosition
    {
        return new JobPosition(
            new JobPositionHash(JobApplicationMother::BACKEND_JOB_POSITION_HASH),
            'Senior Backend Engineer (PHP)',
            'Design and evolve our modular monolith.',
            ['PHP', 'Symfony', 'DDD'],
        );
    }
}
