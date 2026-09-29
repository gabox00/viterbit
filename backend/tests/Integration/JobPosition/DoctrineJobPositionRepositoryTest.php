<?php

declare(strict_types=1);

namespace App\Tests\Integration\JobPosition;

use App\JobPosition\Domain\Entity\JobPosition;
use App\JobPosition\Domain\Repository\IJobPositionRepository;
use App\JobPosition\Domain\ValueObject\JobPositionHash;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineJobPositionRepositoryTest extends KernelTestCase
{
    public function testListsTheSeededCatalogSortedByTitle(): void
    {
        $titles = array_map(static fn (JobPosition $jobPosition): string => $jobPosition->title, $this->repository()->all());

        self::assertSame(
            ['DevOps Engineer', 'Frontend Engineer (React)', 'QA Automation Engineer', 'Senior Backend Engineer (PHP)'],
            $titles,
        );
    }

    public function testFindsAJobPositionWithItsRequiredSkills(): void
    {
        $jobPosition = $this->repository()->findByHash(new JobPositionHash('0199a0e0-0000-7000-8000-000000000001'));

        self::assertSame('Senior Backend Engineer (PHP)', $jobPosition?->title);
        self::assertContains('Symfony', $jobPosition->requiredSkills);
    }

    public function testReturnsNullForUnknownJobPositions(): void
    {
        self::assertNull($this->repository()->findByHash(new JobPositionHash('0199a0e0-0000-7000-8000-000000000099')));
    }

    private function repository(): IJobPositionRepository
    {
        /** @var IJobPositionRepository */
        return self::getContainer()->get(IJobPositionRepository::class);
    }
}
