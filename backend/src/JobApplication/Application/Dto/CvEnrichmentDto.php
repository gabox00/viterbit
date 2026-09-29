<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Dto;

final readonly class CvEnrichmentDto
{
    public function __construct(
        public string $summary,
        public int $score,
    ) {
    }
}
