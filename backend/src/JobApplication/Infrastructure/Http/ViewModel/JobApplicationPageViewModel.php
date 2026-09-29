<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Http\ViewModel;

use App\JobApplication\Application\Dto\JobApplicationPageDto;

final readonly class JobApplicationPageViewModel
{
    /** @param list<JobApplicationSummaryViewModel> $items */
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $perPage,
    ) {
    }

    public static function fromDto(JobApplicationPageDto $page): self
    {
        return new self(
            array_map(JobApplicationSummaryViewModel::fromDto(...), $page->items),
            $page->total,
            $page->page,
            $page->perPage,
        );
    }
}
