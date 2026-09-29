<?php

declare(strict_types=1);

namespace App\Tests\Double;

use App\JobApplication\Domain\Entity\JobApplication;
use App\JobApplication\Domain\Repository\IJobApplicationRepository;
use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\Shared\Domain\Criteria\Criteria;

final class InMemoryJobApplicationRepository implements IJobApplicationRepository
{
    /** @var array<string, JobApplication> */
    private array $jobApplications = [];

    public function save(JobApplication $jobApplication): void
    {
        $this->jobApplications[$jobApplication->hash->value] = $jobApplication;
    }

    public function findByHash(JobApplicationHash $hash): ?JobApplication
    {
        return $this->jobApplications[$hash->value] ?? null;
    }

    public function search(Criteria $criteria): array
    {
        throw new \LogicException('Use the DBAL repository to test criteria searches.');
    }

    public function count(Criteria $criteria): int
    {
        throw new \LogicException('Use the DBAL repository to test criteria searches.');
    }
}
