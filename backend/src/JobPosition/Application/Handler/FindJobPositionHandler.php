<?php

declare(strict_types=1);

namespace App\JobPosition\Application\Handler;

use App\JobPosition\Application\Dto\JobPositionDto;
use App\JobPosition\Application\Query\FindJobPositionQuery;
use App\JobPosition\Domain\Exception\JobPositionNotFoundException;
use App\JobPosition\Domain\Repository\IJobPositionRepository;
use App\JobPosition\Domain\ValueObject\JobPositionHash;
use App\Shared\Application\Bus\Query\IQueryHandler;

final readonly class FindJobPositionHandler implements IQueryHandler
{
    public function __construct(private IJobPositionRepository $jobPositionRepository)
    {
    }

    public function __invoke(FindJobPositionQuery $query): JobPositionDto
    {
        $hash = new JobPositionHash($query->hash);
        $jobPosition = $this->jobPositionRepository->findByHash($hash) ?? throw JobPositionNotFoundException::withHash($hash);

        return JobPositionDto::fromJobPosition($jobPosition);
    }
}
