<?php

declare(strict_types=1);

namespace App\JobPosition\Application\Handler;

use App\JobPosition\Application\Dto\JobPositionDto;
use App\JobPosition\Application\Query\ListJobPositionsQuery;
use App\JobPosition\Domain\Repository\IJobPositionRepository;
use App\Shared\Application\Bus\Query\IQueryHandler;

final readonly class ListJobPositionsHandler implements IQueryHandler
{
    public function __construct(private IJobPositionRepository $jobPositionRepository)
    {
    }

    /** @return list<JobPositionDto> */
    public function __invoke(ListJobPositionsQuery $query): array
    {
        return array_map(JobPositionDto::fromJobPosition(...), $this->jobPositionRepository->all());
    }
}
