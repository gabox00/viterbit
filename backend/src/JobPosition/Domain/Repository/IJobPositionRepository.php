<?php

declare(strict_types=1);

namespace App\JobPosition\Domain\Repository;

use App\JobPosition\Domain\Entity\JobPosition;
use App\JobPosition\Domain\ValueObject\JobPositionHash;

interface IJobPositionRepository
{
    /** @return list<JobPosition> */
    public function all(): array;

    public function findByHash(JobPositionHash $hash): ?JobPosition;
}
