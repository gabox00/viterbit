<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Handler;

use App\JobApplication\Application\Dto\JobApplicationPageDto;
use App\JobApplication\Application\Dto\JobApplicationSummaryDto;
use App\JobApplication\Application\Query\SearchJobApplicationsQuery;
use App\JobApplication\Domain\Repository\IJobApplicationRepository;
use App\Shared\Application\Bus\Query\IQueryHandler;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter;
use App\Shared\Domain\Criteria\Order;

final readonly class SearchJobApplicationsHandler implements IQueryHandler
{
    public function __construct(private IJobApplicationRepository $jobApplicationRepository)
    {
    }

    public function __invoke(SearchJobApplicationsQuery $query): JobApplicationPageDto
    {
        $filters = $this->filtersFrom($query);
        $pageCriteria = new Criteria(
            $filters,
            Order::desc('appliedAt'),
            $query->perPage,
            ($query->page - 1) * $query->perPage,
        );

        return new JobApplicationPageDto(
            array_map(JobApplicationSummaryDto::fromJobApplication(...), $this->jobApplicationRepository->search($pageCriteria)),
            $this->jobApplicationRepository->count(new Criteria($filters)),
            $query->page,
            $query->perPage,
        );
    }

    /** @return list<Filter> */
    private function filtersFrom(SearchJobApplicationsQuery $query): array
    {
        $filters = [];
        if (null !== $query->status) {
            $filters[] = Filter::equal('status', $query->status);
        }
        if (null !== $query->jobPositionHash) {
            $filters[] = Filter::equal('jobPositionHash', $query->jobPositionHash);
        }
        if (null !== $query->search && '' !== trim($query->search)) {
            $filters[] = Filter::contains('candidate', trim($query->search));
        }

        return $filters;
    }
}
