<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Repository;

use App\JobApplication\Domain\Entity\JobApplication;
use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\Shared\Domain\Criteria\Criteria;

interface IJobApplicationRepository
{
    public function save(JobApplication $jobApplication): void;

    public function findByHash(JobApplicationHash $hash): ?JobApplication;

    /** @return list<JobApplication> */
    public function search(Criteria $criteria): array;

    public function count(Criteria $criteria): int;
}
