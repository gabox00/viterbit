<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Handler;

use App\JobApplication\Application\Dto\JobApplicationDetailDto;
use App\JobApplication\Application\Query\FindJobApplicationQuery;
use App\JobApplication\Domain\Exception\JobApplicationNotFoundException;
use App\JobApplication\Domain\Repository\IJobApplicationRepository;
use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\Shared\Application\Bus\Query\IQueryHandler;

final readonly class FindJobApplicationHandler implements IQueryHandler
{
    public function __construct(private IJobApplicationRepository $jobApplicationRepository)
    {
    }

    public function __invoke(FindJobApplicationQuery $query): JobApplicationDetailDto
    {
        $hash = new JobApplicationHash($query->hash);
        $jobApplication = $this->jobApplicationRepository->findByHash($hash) ?? throw JobApplicationNotFoundException::withHash($hash);

        return JobApplicationDetailDto::fromJobApplication($jobApplication);
    }
}
