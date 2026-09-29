<?php

declare(strict_types=1);

namespace App\Tests;

use App\JobPosition\Domain\Entity\JobPosition;
use App\JobPosition\Domain\Repository\IJobPositionRepository;
use App\JobPosition\Domain\ValueObject\JobPositionHash;
use Doctrine\ORM\EntityManagerInterface;

trait CleansDatabase
{
    protected function cleanDatabase(): void
    {
        $this->entityManager()->getConnection()->executeStatement('DELETE FROM job_application');
        $this->entityManager()->clear();
    }

    protected function entityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface */
        return static::getContainer()->get(EntityManagerInterface::class);
    }

    /** A seeded position managed by the entity manager, so job applications can reference it on flush. */
    protected function jobPosition(string $hash): JobPosition
    {
        /** @var IJobPositionRepository $repository */
        $repository = static::getContainer()->get(IJobPositionRepository::class);

        return $repository->findByHash(new JobPositionHash($hash)) ?? throw new \LogicException(sprintf('Seeded position "%s" not found.', $hash));
    }
}
