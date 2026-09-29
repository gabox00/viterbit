<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Dto;

use App\JobApplication\Domain\Entity\JobApplication;

final readonly class JobApplicationSummaryDto
{
    public function __construct(
        public string $hash,
        public string $jobPositionHash,
        public string $candidateFullName,
        public string $candidateEmail,
        public string $status,
        public ?int $aiScore,
        public \DateTimeImmutable $appliedAt,
    ) {
    }

    public static function fromJobApplication(JobApplication $jobApplication): self
    {
        return new self(
            $jobApplication->hash->value,
            $jobApplication->jobPosition->hash->value,
            $jobApplication->candidateFullName,
            $jobApplication->candidateEmail->value,
            $jobApplication->status->value,
            $jobApplication->aiScore?->value,
            $jobApplication->appliedAt,
        );
    }
}
