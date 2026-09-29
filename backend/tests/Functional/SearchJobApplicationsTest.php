<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\JobApplication\Domain\Repository\IJobApplicationRepository;
use App\JobApplication\Domain\ValueObject\AiScore;
use App\Tests\Mother\JobApplicationMother;

final class SearchJobApplicationsTest extends ApiTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        /** @var IJobApplicationRepository $repository */
        $repository = self::getContainer()->get(IJobApplicationRepository::class);
        $enriched = JobApplicationMother::submitted(jobPosition: $this->jobPosition(JobApplicationMother::BACKEND_JOB_POSITION_HASH), candidateFullName: 'Grace Hopper', candidateEmail: 'grace@navy.mil', appliedAt: '2026-09-02 10:00:00');
        $enriched->attachEnrichment('Summary.', new AiScore(90), new \DateTimeImmutable());
        $repository->save($enriched);
        $repository->save(JobApplicationMother::submitted(jobPosition: $this->jobPosition(JobApplicationMother::BACKEND_JOB_POSITION_HASH), candidateFullName: 'Ada Lovelace', appliedAt: '2026-09-01 10:00:00'));
        $repository->save(JobApplicationMother::submitted(
            jobPosition: $this->jobPosition(JobApplicationMother::FRONTEND_JOB_POSITION_HASH),
            candidateFullName: 'Alan Turing',
            candidateEmail: 'alan@bletchley.uk',
            appliedAt: '2026-09-03 10:00:00',
        ));
    }

    public function testListsNewestFirstWithPaginationMetadata(): void
    {
        $page = $this->getJson('/api/v1/job-applications');

        self::assertResponseIsSuccessful();
        self::assertSame(['Alan Turing', 'Grace Hopper', 'Ada Lovelace'], $this->names($page));
        self::assertSame(3, $page['total']);
        self::assertSame(1, $page['page']);
        self::assertSame(20, $page['per_page']);
    }

    public function testExposesTheScoreInTheList(): void
    {
        $page = $this->getJson('/api/v1/job-applications?status=enriched');

        self::assertIsArray($page['items']);
        self::assertIsArray($page['items'][0]);
        self::assertSame(90, $page['items'][0]['ai_score']);
    }

    public function testFiltersByStatus(): void
    {
        self::assertSame(['Alan Turing', 'Ada Lovelace'], $this->names($this->getJson('/api/v1/job-applications?status=received')));
    }

    public function testFiltersByJobPosition(): void
    {
        self::assertSame(['Alan Turing'], $this->names($this->getJson('/api/v1/job-applications?job_position_hash='.JobApplicationMother::FRONTEND_JOB_POSITION_HASH)));
    }

    public function testSearchesByNameOrEmail(): void
    {
        self::assertSame(['Grace Hopper'], $this->names($this->getJson('/api/v1/job-applications?search=hopper')));
        self::assertSame(['Alan Turing'], $this->names($this->getJson('/api/v1/job-applications?search=bletchley')));
    }

    public function testCombinesFiltersAndPaginates(): void
    {
        $page = $this->getJson('/api/v1/job-applications?status=received&search=a&page=2&per_page=1');

        self::assertSame(['Ada Lovelace'], $this->names($page));
        self::assertSame(2, $page['total']);
    }

    public function testRejectsInvalidFilters(): void
    {
        $this->client->request('GET', '/api/v1/job-applications?status=hired');

        self::assertResponseStatusCodeSame(422);
    }

    /**
     * @param array<mixed> $page
     *
     * @return list<string>
     */
    private function names(array $page): array
    {
        self::assertIsArray($page['items']);

        return array_values(array_map(
            static fn (mixed $item): string => is_array($item) && is_string($item['candidate_full_name']) ? $item['candidate_full_name'] : '',
            $page['items'],
        ));
    }
}
