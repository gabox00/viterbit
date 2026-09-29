<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Worker;

use App\JobApplication\Application\Service\CvEnricher;
use App\JobApplication\Domain\Event\JobApplicationSubmitted;
use App\JobApplication\Domain\Exception\JobApplicationNotFoundException;
use App\JobApplication\Domain\Repository\IJobApplicationRepository;
use App\JobApplication\Domain\ValueObject\AiScore;
use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\Shared\Application\Bus\Event\IDomainEventWorker;
use Psr\Clock\ClockInterface;

final readonly class EnrichCvOnJobApplicationSubmittedWorker implements IDomainEventWorker
{
    public function __construct(
        private IJobApplicationRepository $jobApplicationRepository,
        private CvEnricher $cvEnricher,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(JobApplicationSubmitted $event): void
    {
        $hash = new JobApplicationHash($event->jobApplicationHash);
        $jobApplication = $this->jobApplicationRepository->findByHash($hash) ?? throw JobApplicationNotFoundException::withHash($hash);

        $enrichment = $this->cvEnricher->enrich($jobApplication);
        $jobApplication->attachEnrichment($enrichment->summary, new AiScore($enrichment->score), $this->clock->now());

        $this->jobApplicationRepository->save($jobApplication);
    }
}
