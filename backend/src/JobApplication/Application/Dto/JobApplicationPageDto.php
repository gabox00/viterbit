<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Dto;

final readonly class JobApplicationPageDto
{
    /** @param list<JobApplicationSummaryDto> $items */
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $perPage,
    ) {
    }
}
