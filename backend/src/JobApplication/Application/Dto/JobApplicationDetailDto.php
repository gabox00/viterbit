<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Dto;

use App\JobApplication\Domain\Entity\JobApplication;

final readonly class JobApplicationDetailDto
{
    public function __construct(
        public string $hash,
        public string $jobPositionHash,
        public string $candidateFullName,
        public string $candidateEmail,
        public ?string $candidatePhone,
        public ?string $notes,
        public string $cvText,
        public string $status,
        public ?string $aiSummary,
        public ?int $aiScore,
        public \DateTimeImmutable $appliedAt,
        public ?\DateTimeImmutable $enrichedAt,
    ) {
    }

    public static function fromJobApplication(JobApplication $jobApplication): self
    {
        return new self(
            $jobApplication->hash->value,
            $jobApplication->jobPosition->hash->value,
            $jobApplication->candidateFullName,
            $jobApplication->candidateEmail->value,
            $jobApplication->candidatePhone,
            $jobApplication->notes,
            $jobApplication->cvText,
            $jobApplication->status->value,
            $jobApplication->aiSummary,
            $jobApplication->aiScore?->value,
            $jobApplication->appliedAt,
            $jobApplication->enrichedAt,
        );
    }
}
