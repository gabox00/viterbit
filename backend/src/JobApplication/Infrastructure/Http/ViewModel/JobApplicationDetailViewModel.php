<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Http\ViewModel;

use App\JobApplication\Application\Dto\JobApplicationDetailDto;

final readonly class JobApplicationDetailViewModel
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
        public string $appliedAt,
        public ?string $enrichedAt,
    ) {
    }

    public static function fromDto(JobApplicationDetailDto $jobApplication): self
    {
        return new self(
            $jobApplication->hash,
            $jobApplication->jobPositionHash,
            $jobApplication->candidateFullName,
            $jobApplication->candidateEmail,
            $jobApplication->candidatePhone,
            $jobApplication->notes,
            $jobApplication->cvText,
            $jobApplication->status,
            $jobApplication->aiSummary,
            $jobApplication->aiScore,
            $jobApplication->appliedAt->format(\DateTimeInterface::ATOM),
            $jobApplication->enrichedAt?->format(\DateTimeInterface::ATOM),
        );
    }
}
