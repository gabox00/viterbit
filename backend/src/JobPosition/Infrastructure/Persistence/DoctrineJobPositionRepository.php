<?php

declare(strict_types=1);

namespace App\JobPosition\Infrastructure\Persistence;

use App\JobPosition\Domain\Entity\JobPosition;
use App\JobPosition\Domain\Repository\IJobPositionRepository;
use App\JobPosition\Domain\ValueObject\JobPositionHash;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final readonly class DoctrineJobPositionRepository implements IJobPositionRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function all(): array
    {
        return $this->repository()->findBy([], ['title' => 'ASC']);
    }

    public function findByHash(JobPositionHash $hash): ?JobPosition
    {
        return $this->repository()->findOneBy(['hash' => $hash]);
    }

    /** @return EntityRepository<JobPosition> */
    private function repository(): EntityRepository
    {
        return $this->entityManager->getRepository(JobPosition::class);
    }
}
