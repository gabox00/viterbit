<?php

declare(strict_types=1);

namespace App\Tests\Unit\JobApplication\Application;

use App\JobApplication\Application\Service\CvEnricher;
use App\JobApplication\Application\Worker\EnrichCvOnJobApplicationSubmittedWorker;
use App\JobApplication\Domain\Enum\JobApplicationStatusEnum;
use App\JobApplication\Domain\Event\JobApplicationSubmitted;
use App\JobApplication\Domain\Exception\JobApplicationNotFoundException;
use App\JobApplication\Domain\Repository\IJobApplicationRepository;
use App\Shared\Infrastructure\Llm\Mock\MockLlmClient;
use App\Tests\Double\InMemoryJobApplicationRepository;
use App\Tests\Mother\JobApplicationMother;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class EnrichCvOnJobApplicationSubmittedWorkerTest extends TestCase
{
    public function testAttachesTheSummaryAndScoreOfTheSubmittedCv(): void
    {
        $repository = new InMemoryJobApplicationRepository();
        $jobApplication = JobApplicationMother::submitted();
        $repository->save($jobApplication);

        self::worker($repository, new MockClock('2026-09-28 10:00:05'))(self::submittedEvent($jobApplication->hash->value));

        $enriched = $repository->findByHash($jobApplication->hash);
        self::assertSame(JobApplicationStatusEnum::Enriched, $enriched?->status);
        self::assertStringStartsWith('Senior PHP developer', (string) $enriched->aiSummary);
        self::assertSame(100, $enriched->aiScore?->value);
        self::assertSame('2026-09-28 10:00:05', $enriched->enrichedAt?->format('Y-m-d H:i:s'));
    }

    public function testPersistsTheEnrichedApplication(): void
    {
        $jobApplication = JobApplicationMother::submitted();
        $repository = $this->createMock(IJobApplicationRepository::class);
        $repository->method('findByHash')->willReturn($jobApplication);
        $repository->expects(self::once())->method('save')->with($jobApplication);

        self::worker($repository, new MockClock())(self::submittedEvent($jobApplication->hash->value));
    }

    public function testFailsForUnknownApplicationsSoTheMessageIsRetried(): void
    {
        $this->expectException(JobApplicationNotFoundException::class);

        self::worker(new InMemoryJobApplicationRepository(), new MockClock())(self::submittedEvent('0199a0e0-1111-7000-8000-000000000009'));
    }

    private static function worker(IJobApplicationRepository $repository, MockClock $clock): EnrichCvOnJobApplicationSubmittedWorker
    {
        return new EnrichCvOnJobApplicationSubmittedWorker($repository, new CvEnricher(new MockLlmClient(0)), $clock);
    }

    private static function submittedEvent(string $jobApplicationHash): JobApplicationSubmitted
    {
        return new JobApplicationSubmitted($jobApplicationHash, JobApplicationMother::BACKEND_JOB_POSITION_HASH, JobApplicationMother::CV_TEXT);
    }
}
