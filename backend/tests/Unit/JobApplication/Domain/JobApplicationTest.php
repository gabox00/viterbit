<?php

declare(strict_types=1);

namespace App\Tests\Unit\JobApplication\Domain;

use App\JobApplication\Domain\Entity\JobApplication;
use App\JobApplication\Domain\Enum\JobApplicationStatusEnum;
use App\JobApplication\Domain\Event\JobApplicationSubmitted;
use App\JobApplication\Domain\ValueObject\AiScore;
use App\JobApplication\Domain\ValueObject\Email;
use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\Shared\Domain\Exception\InvalidValueException;
use App\Tests\Mother\JobApplicationMother;
use App\Tests\Mother\JobPositionMother;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class JobApplicationTest extends TestCase
{
    public function testSubmitStartsAsReceivedWithoutEnrichment(): void
    {
        $jobApplication = JobApplicationMother::submitted(appliedAt: '2026-09-28 10:00:00');

        self::assertSame(JobApplicationStatusEnum::Received, $jobApplication->status);
        self::assertNull($jobApplication->aiSummary);
        self::assertNull($jobApplication->aiScore);
        self::assertNull($jobApplication->enrichedAt);
        self::assertSame('2026-09-28 10:00:00', $jobApplication->appliedAt->format('Y-m-d H:i:s'));
    }

    public function testSubmitRecordsJobApplicationSubmittedEvent(): void
    {
        $jobApplication = JobApplicationMother::submitted(hash: '0199a0e0-1111-7000-8000-000000000001');

        $events = $jobApplication->pullDomainEvents();

        self::assertEquals(
            [new JobApplicationSubmitted('0199a0e0-1111-7000-8000-000000000001', JobApplicationMother::BACKEND_JOB_POSITION_HASH, JobApplicationMother::CV_TEXT)],
            $events,
        );
        self::assertSame([], $jobApplication->pullDomainEvents());
    }

    public function testSubmitTrimsCandidateNameAndCvText(): void
    {
        $jobApplication = JobApplication::submit(
            new JobApplicationHash('0199a0e0-1111-7000-8000-000000000001'),
            JobPositionMother::backend(),
            '  Grace Hopper  ',
            new Email('grace@example.com'),
            null,
            null,
            "  COBOL pioneer.\n",
            new \DateTimeImmutable(),
        );

        self::assertSame('Grace Hopper', $jobApplication->candidateFullName);
        self::assertSame('COBOL pioneer.', $jobApplication->cvText);
    }

    /** @return iterable<string, array{string, string}> */
    public static function blankFields(): iterable
    {
        yield 'blank name' => [' ', 'A valid CV text.'];
        yield 'blank cv' => ['Grace Hopper', "\n\t "];
    }

    #[DataProvider('blankFields')]
    public function testSubmitRejectsBlankRequiredFields(string $candidateFullName, string $cvText): void
    {
        $this->expectException(InvalidValueException::class);

        JobApplication::submit(
            new JobApplicationHash('0199a0e0-1111-7000-8000-000000000001'),
            JobPositionMother::backend(),
            $candidateFullName,
            new Email('grace@example.com'),
            null,
            null,
            $cvText,
            new \DateTimeImmutable(),
        );
    }

    public function testAttachEnrichmentMarksItAsEnriched(): void
    {
        $jobApplication = JobApplicationMother::submitted();
        $enrichedAt = new \DateTimeImmutable('2026-09-28 10:00:05');

        $jobApplication->attachEnrichment('A concise summary.', new AiScore(86), $enrichedAt);

        self::assertSame(JobApplicationStatusEnum::Enriched, $jobApplication->status);
        self::assertSame('A concise summary.', $jobApplication->aiSummary);
        self::assertSame(86, $jobApplication->aiScore?->value);
        self::assertSame($enrichedAt, $jobApplication->enrichedAt);
    }
}
