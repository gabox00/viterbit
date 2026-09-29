<?php

declare(strict_types=1);

namespace App\Tests\Integration\JobApplication;

use App\JobApplication\Application\Dto\JobApplicationDetailDto;
use App\JobApplication\Domain\Entity\JobApplication;
use App\JobApplication\Domain\ValueObject\AiScore;
use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\JobApplication\Infrastructure\Persistence\DoctrineJobApplicationRepository;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter;
use App\Shared\Domain\Criteria\Order;
use App\Tests\CleansDatabase;
use App\Tests\Mother\JobApplicationMother;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineJobApplicationRepositoryTest extends KernelTestCase
{
    use CleansDatabase;

    private DoctrineJobApplicationRepository $repository;

    protected function setUp(): void
    {
        $this->cleanDatabase();
        $this->repository = new DoctrineJobApplicationRepository($this->entityManager());
    }

    public function testSavesAndFindsAJobApplication(): void
    {
        $jobApplication = $this->submitted();

        $this->repository->save($jobApplication);
        $this->entityManager()->clear();

        $this->assertPersisted($jobApplication);
    }

    public function testReturnsNullForUnknownIds(): void
    {
        self::assertNull($this->repository->findByHash(new JobApplicationHash('0199a0e0-1111-7000-8000-000000000009')));
    }

    public function testUpdatesTheEnrichmentOfAnExistingApplication(): void
    {
        $jobApplication = $this->submitted();
        $this->repository->save($jobApplication);

        $jobApplication->attachEnrichment('Summary.', new AiScore(64), new \DateTimeImmutable('2026-09-28 10:00:04', new \DateTimeZone('UTC')));
        $this->repository->save($jobApplication);
        $this->entityManager()->clear();

        $this->assertPersisted($jobApplication);
        self::assertSame(1, $this->repository->count(new Criteria()));
    }

    public function testSearchesNewestFirst(): void
    {
        $this->saveAll(
            $this->submitted(candidateFullName: 'Oldest', appliedAt: '2026-09-01 10:00:00'),
            $this->submitted(candidateFullName: 'Newest', appliedAt: '2026-09-03 10:00:00'),
            $this->submitted(candidateFullName: 'Middle', appliedAt: '2026-09-02 10:00:00'),
        );

        self::assertSame(['Newest', 'Middle', 'Oldest'], $this->namesMatching(new Criteria(order: Order::desc('appliedAt'))));
    }

    public function testFiltersByStatusAndJobPosition(): void
    {
        $enriched = $this->submitted(candidateFullName: 'Enriched backend');
        $enriched->attachEnrichment('Summary.', new AiScore(50), new \DateTimeImmutable());
        $this->saveAll(
            $enriched,
            $this->submitted(candidateFullName: 'Received backend'),
            $this->submitted(jobPositionHash: JobApplicationMother::FRONTEND_JOB_POSITION_HASH, candidateFullName: 'Received frontend'),
        );

        self::assertSame(['Enriched backend'], $this->namesMatching(new Criteria([Filter::equal('status', 'enriched')])));
        self::assertSame(['Received frontend'], $this->namesMatching(new Criteria([
            Filter::equal('status', 'received'),
            Filter::equal('jobPositionHash', JobApplicationMother::FRONTEND_JOB_POSITION_HASH),
        ])));
    }

    public function testSearchesByNameOrEmailCaseInsensitively(): void
    {
        $this->saveAll(
            $this->submitted(candidateFullName: 'Ada Lovelace', candidateEmail: 'ada@example.com'),
            $this->submitted(candidateFullName: 'Grace Hopper', candidateEmail: 'grace@navy.mil'),
            $this->submitted(candidateFullName: 'Alan Turing', candidateEmail: 'alan@bletchley.uk'),
        );

        self::assertSame(['Ada Lovelace'], $this->namesMatching(new Criteria([Filter::contains('candidate', 'LOVE')])));
        self::assertSame(['Grace Hopper'], $this->namesMatching(new Criteria([Filter::contains('candidate', 'navy')])));
        self::assertSame(2, $this->repository->count(new Criteria([Filter::contains('candidate', 'ace')])));
    }

    public function testTreatsLikeWildcardsAsLiterals(): void
    {
        $this->saveAll(
            $this->submitted(candidateFullName: 'Name with 100% match'),
            $this->submitted(candidateFullName: 'Name with 1000 match'),
        );

        self::assertSame(['Name with 100% match'], $this->namesMatching(new Criteria([Filter::contains('candidate', '100%')])));
        self::assertSame([], $this->namesMatching(new Criteria([Filter::contains('candidate', '_')])));
    }

    public function testPaginatesAndCountsIgnoringPagination(): void
    {
        foreach (range(1, 5) as $day) {
            $this->repository->save($this->submitted(candidateFullName: 'Candidate '.$day, appliedAt: sprintf('2026-09-0%d 10:00:00', $day)));
        }

        $criteria = new Criteria(order: Order::desc('appliedAt'), limit: 2, offset: 2);

        self::assertSame(['Candidate 3', 'Candidate 2'], $this->namesMatching($criteria));
        self::assertSame(5, $this->repository->count($criteria));
    }

    public function testRejectsUnknownCriteriaFields(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->repository->search(new Criteria([Filter::equal('cvText', 'anything')]));
    }

    private function submitted(
        string $jobPositionHash = JobApplicationMother::BACKEND_JOB_POSITION_HASH,
        string $candidateFullName = 'Ada Lovelace',
        string $candidateEmail = 'ada@example.com',
        string $appliedAt = '2026-09-28 10:00:00',
    ): JobApplication {
        return JobApplicationMother::submitted(
            jobPosition: $this->jobPosition($jobPositionHash),
            candidateFullName: $candidateFullName,
            candidateEmail: $candidateEmail,
            appliedAt: $appliedAt,
        );
    }

    private function saveAll(JobApplication ...$jobApplications): void
    {
        foreach ($jobApplications as $jobApplication) {
            $this->repository->save($jobApplication);
        }
    }

    /** @return list<string> */
    private function namesMatching(Criteria $criteria): array
    {
        return array_map(
            static fn (JobApplication $jobApplication): string => $jobApplication->candidateFullName,
            $this->repository->search($criteria),
        );
    }

    /** Compares through the detail DTO: it covers every mapped field and resolves the lazy job position relation. */
    private function assertPersisted(JobApplication $expected): void
    {
        $found = $this->repository->findByHash($expected->hash);

        self::assertNotNull($found);
        self::assertSame($expected->id, $found->id);
        self::assertEquals(JobApplicationDetailDto::fromJobApplication($expected), JobApplicationDetailDto::fromJobApplication($found));
    }
}
