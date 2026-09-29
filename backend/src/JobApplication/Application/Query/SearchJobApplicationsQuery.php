<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Query;

use App\JobApplication\Application\Dto\JobApplicationPageDto;
use App\Shared\Application\Bus\Query\IQuery;

/** @implements IQuery<JobApplicationPageDto> */
final readonly class SearchJobApplicationsQuery implements IQuery
{
    public function __construct(
        public ?string $status,
        public ?string $jobPositionHash,
        public ?string $search,
        public int $page,
        public int $perPage,
    ) {
    }
}
