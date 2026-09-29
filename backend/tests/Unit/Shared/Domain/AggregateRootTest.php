<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain;

use App\JobApplication\Domain\Event\JobApplicationSubmitted;
use App\Shared\Domain\AggregateRoot;
use PHPUnit\Framework\TestCase;

final class AggregateRootTest extends TestCase
{
    public function testPullsEveryRecordedEventInOrderOnlyOnce(): void
    {
        $first = new JobApplicationSubmitted('0199a0e0-1111-7000-8000-000000000001', '0199a0e0-0000-7000-8000-000000000001', 'First CV.');
        $second = new JobApplicationSubmitted('0199a0e0-1111-7000-8000-000000000002', '0199a0e0-0000-7000-8000-000000000001', 'Second CV.');
        $aggregate = new class extends AggregateRoot {
            public function recordAll(JobApplicationSubmitted ...$events): void
            {
                foreach ($events as $event) {
                    $this->record($event);
                }
            }
        };

        $aggregate->recordAll($first, $second);

        self::assertSame([$first, $second], $aggregate->pullDomainEvents());
        self::assertSame([], $aggregate->pullDomainEvents());
    }
}
