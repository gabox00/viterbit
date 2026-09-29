<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Http\ViewModel;

use App\JobApplication\Application\Dto\JobApplicationSummaryDto;

final readonly class JobApplicationSummaryViewModel
{
    public function __construct(
        public string $hash,
        public string $jobPositionHash,
        public string $candidateFullName,
        public string $candidateEmail,
        public string $status,
        public ?int $aiScore,
        public string $appliedAt,
    ) {
    }

    public static function fromDto(JobApplicationSummaryDto $jobApplication): self
    {
        return new self(
            $jobApplication->hash,
            $jobApplication->jobPositionHash,
            $jobApplication->candidateFullName,
            $jobApplication->candidateEmail,
            $jobApplication->status,
            $jobApplication->aiScore,
            $jobApplication->appliedAt->format(\DateTimeInterface::ATOM),
        );
    }
}
