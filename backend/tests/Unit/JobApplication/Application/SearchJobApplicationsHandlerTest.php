<?php

declare(strict_types=1);

namespace App\Tests\Unit\JobApplication\Application;

use App\JobApplication\Application\Handler\SearchJobApplicationsHandler;
use App\JobApplication\Application\Query\SearchJobApplicationsQuery;
use App\JobApplication\Domain\Repository\IJobApplicationRepository;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter;
use App\Shared\Domain\Criteria\Order;
use App\Tests\Mother\JobApplicationMother;
use PHPUnit\Framework\TestCase;

final class SearchJobApplicationsHandlerTest extends TestCase
{
    public function testBuildsNewestFirstPaginatedCriteriaWithAllFilters(): void
    {
        $expectedFilters = [
            Filter::equal('status', 'enriched'),
            Filter::equal('jobPositionHash', JobApplicationMother::BACKEND_JOB_POSITION_HASH),
            Filter::contains('candidate', 'ada'),
        ];
        $jobApplication = JobApplicationMother::submitted();

        $repository = $this->createMock(IJobApplicationRepository::class);
        $repository->expects(self::once())->method('search')
            ->with(new Criteria($expectedFilters, Order::desc('appliedAt'), 10, 20))
            ->willReturn([$jobApplication]);
        $repository->expects(self::once())->method('count')
            ->with(new Criteria($expectedFilters))
            ->willReturn(21);

        $page = new SearchJobApplicationsHandler($repository)(
            new SearchJobApplicationsQuery('enriched', JobApplicationMother::BACKEND_JOB_POSITION_HASH, '  ada ', 3, 10),
        );

        self::assertSame(21, $page->total);
        self::assertSame(3, $page->page);
        self::assertSame(10, $page->perPage);
        self::assertCount(1, $page->items);
        self::assertSame($jobApplication->hash->value, $page->items[0]->hash);
        self::assertSame('received', $page->items[0]->status);
    }

    public function testIgnoresMissingAndBlankFilters(): void
    {
        $repository = $this->createMock(IJobApplicationRepository::class);
        $repository->expects(self::once())->method('search')
            ->with(new Criteria([], Order::desc('appliedAt'), 20, 0))
            ->willReturn([]);
        $repository->expects(self::once())->method('count')->with(new Criteria())->willReturn(0);

        $page = new SearchJobApplicationsHandler($repository)(new SearchJobApplicationsQuery(null, null, '   ', 1, 20));

        self::assertSame([], $page->items);
        self::assertSame(0, $page->total);
    }
}
