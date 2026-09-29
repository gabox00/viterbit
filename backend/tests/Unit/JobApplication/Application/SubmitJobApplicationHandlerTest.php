<?php

declare(strict_types=1);

namespace App\Tests\Unit\JobApplication\Application;

use App\JobApplication\Application\Command\SubmitJobApplicationCommand;
use App\JobApplication\Application\Exception\UnknownJobPositionException;
use App\JobApplication\Application\Handler\SubmitJobApplicationHandler;
use App\JobApplication\Domain\Enum\JobApplicationStatusEnum;
use App\JobApplication\Domain\Event\JobApplicationSubmitted;
use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\JobPosition\Domain\Entity\JobPosition;
use App\JobPosition\Domain\Repository\IJobPositionRepository;
use App\JobPosition\Domain\ValueObject\JobPositionHash;
use App\Tests\Double\InMemoryJobApplicationRepository;
use App\Tests\Double\SpyEventBus;
use App\Tests\Mother\JobApplicationMother;
use App\Tests\Mother\JobPositionMother;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class SubmitJobApplicationHandlerTest extends TestCase
{
    private const string ID = '0199a0e0-1111-7000-8000-000000000001';

    private InMemoryJobApplicationRepository $repository;
    private SpyEventBus $eventBus;

    protected function setUp(): void
    {
        $this->repository = new InMemoryJobApplicationRepository();
        $this->eventBus = new SpyEventBus();
    }

    public function testStoresTheApplicationAsReceivedAndPublishesTheEvent(): void
    {
        $this->handler(jobPositionExists: true)(self::command());

        $jobApplication = $this->repository->findByHash(new JobApplicationHash(self::ID));
        self::assertNotNull($jobApplication);
        self::assertSame(JobApplicationStatusEnum::Received, $jobApplication->status);
        self::assertSame('2026-09-28 10:00:00', $jobApplication->appliedAt->format('Y-m-d H:i:s'));
        self::assertSame('ada@example.com', $jobApplication->candidateEmail->value);
        self::assertEquals(
            [new JobApplicationSubmitted(self::ID, JobApplicationMother::BACKEND_JOB_POSITION_HASH, JobApplicationMother::CV_TEXT)],
            $this->eventBus->publishedEvents,
        );
    }

    public function testRejectsUnknownJobPositions(): void
    {
        try {
            $this->handler(jobPositionExists: false)(self::command());
            self::fail('Expected an unknown position to be rejected.');
        } catch (UnknownJobPositionException) {
            self::assertNull($this->repository->findByHash(new JobApplicationHash(self::ID)));
            self::assertSame([], $this->eventBus->publishedEvents);
        }
    }

    private function handler(bool $jobPositionExists): SubmitJobApplicationHandler
    {
        $jobPositionRepository = $this->createStub(IJobPositionRepository::class);
        $jobPositionRepository->method('findByHash')->willReturnCallback(
            static fn (JobPositionHash $jobPositionHash): ?JobPosition => $jobPositionExists && JobApplicationMother::BACKEND_JOB_POSITION_HASH === $jobPositionHash->value
                ? JobPositionMother::backend()
                : null,
        );

        return new SubmitJobApplicationHandler(
            $this->repository,
            $jobPositionRepository,
            $this->eventBus,
            new MockClock('2026-09-28 10:00:00'),
        );
    }

    private static function command(): SubmitJobApplicationCommand
    {
        return new SubmitJobApplicationCommand(
            self::ID,
            JobApplicationMother::BACKEND_JOB_POSITION_HASH,
            'Ada Lovelace',
            'Ada@Example.com',
            null,
            null,
            JobApplicationMother::CV_TEXT,
        );
    }
}
