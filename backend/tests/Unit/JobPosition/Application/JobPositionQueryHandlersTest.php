<?php

declare(strict_types=1);

namespace App\Tests\Unit\JobPosition\Application;

use App\JobPosition\Application\Dto\JobPositionDto;
use App\JobPosition\Application\Handler\FindJobPositionHandler;
use App\JobPosition\Application\Handler\ListJobPositionsHandler;
use App\JobPosition\Application\Query\FindJobPositionQuery;
use App\JobPosition\Application\Query\ListJobPositionsQuery;
use App\JobPosition\Domain\Entity\JobPosition;
use App\JobPosition\Domain\Exception\JobPositionNotFoundException;
use App\JobPosition\Domain\Repository\IJobPositionRepository;
use App\JobPosition\Domain\ValueObject\JobPositionHash;
use PHPUnit\Framework\TestCase;

final class JobPositionQueryHandlersTest extends TestCase
{
    private const string ID = '0199a0e0-0000-7000-8000-000000000001';

    public function testListsAllJobPositions(): void
    {
        $repository = $this->createStub(IJobPositionRepository::class);
        $repository->method('all')->willReturn([self::position()]);

        self::assertEquals(
            [new JobPositionDto(self::ID, 'Backend Engineer', 'Build APIs.', ['PHP'])],
            new ListJobPositionsHandler($repository)(new ListJobPositionsQuery()),
        );
    }

    public function testFindsAJobPositionById(): void
    {
        $repository = $this->createStub(IJobPositionRepository::class);
        $repository->method('findByHash')->willReturnCallback(
            static fn (JobPositionHash $hash): ?JobPosition => self::ID === $hash->value ? self::position() : null,
        );

        self::assertEquals(
            new JobPositionDto(self::ID, 'Backend Engineer', 'Build APIs.', ['PHP']),
            new FindJobPositionHandler($repository)(new FindJobPositionQuery(self::ID)),
        );
    }

    public function testFailsWhenTheJobPositionDoesNotExist(): void
    {
        $repository = $this->createStub(IJobPositionRepository::class);
        $repository->method('findByHash')->willReturn(null);

        $this->expectException(JobPositionNotFoundException::class);

        new FindJobPositionHandler($repository)(new FindJobPositionQuery('0199a0e0-0000-7000-8000-000000000099'));
    }

    private static function position(): JobPosition
    {
        return new JobPosition(new JobPositionHash(self::ID), 'Backend Engineer', 'Build APIs.', ['PHP']);
    }
}
