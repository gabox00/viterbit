<?php

declare(strict_types=1);

namespace App\Tests\Unit\JobApplication\Application;

use App\JobApplication\Application\Handler\FindJobApplicationHandler;
use App\JobApplication\Application\Query\FindJobApplicationQuery;
use App\JobApplication\Domain\Exception\JobApplicationNotFoundException;
use App\JobApplication\Domain\ValueObject\AiScore;
use App\Tests\Double\InMemoryJobApplicationRepository;
use App\Tests\Mother\JobApplicationMother;
use PHPUnit\Framework\TestCase;

final class FindJobApplicationHandlerTest extends TestCase
{
    public function testReturnsAllTheApplicationDetails(): void
    {
        $repository = new InMemoryJobApplicationRepository();
        $jobApplication = JobApplicationMother::submitted();
        $jobApplication->attachEnrichment('Summary.', new AiScore(71), new \DateTimeImmutable('2026-09-28 10:00:03'));
        $repository->save($jobApplication);

        $detail = new FindJobApplicationHandler($repository)(new FindJobApplicationQuery($jobApplication->hash->value));

        self::assertSame($jobApplication->hash->value, $detail->hash);
        self::assertSame(JobApplicationMother::BACKEND_JOB_POSITION_HASH, $detail->jobPositionHash);
        self::assertSame('Ada Lovelace', $detail->candidateFullName);
        self::assertSame('ada@example.com', $detail->candidateEmail);
        self::assertSame('+34 600 000 000', $detail->candidatePhone);
        self::assertSame('Available immediately.', $detail->notes);
        self::assertSame(JobApplicationMother::CV_TEXT, $detail->cvText);
        self::assertSame('enriched', $detail->status);
        self::assertSame('Summary.', $detail->aiSummary);
        self::assertSame(71, $detail->aiScore);
        self::assertSame($jobApplication->appliedAt, $detail->appliedAt);
        self::assertSame('2026-09-28 10:00:03', $detail->enrichedAt?->format('Y-m-d H:i:s'));
    }

    public function testReturnsNoEnrichmentWhileTheApplicationIsReceived(): void
    {
        $repository = new InMemoryJobApplicationRepository();
        $jobApplication = JobApplicationMother::submitted();
        $repository->save($jobApplication);

        $detail = new FindJobApplicationHandler($repository)(new FindJobApplicationQuery($jobApplication->hash->value));

        self::assertSame('received', $detail->status);
        self::assertNull($detail->aiSummary);
        self::assertNull($detail->aiScore);
        self::assertNull($detail->enrichedAt);
    }

    public function testFailsWhenTheApplicationDoesNotExist(): void
    {
        $this->expectException(JobApplicationNotFoundException::class);

        new FindJobApplicationHandler(new InMemoryJobApplicationRepository())(new FindJobApplicationQuery('0199a0e0-1111-7000-8000-000000000009'));
    }
}
