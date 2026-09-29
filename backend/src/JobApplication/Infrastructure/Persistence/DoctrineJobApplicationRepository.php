<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Persistence;

use App\JobApplication\Domain\Entity\JobApplication;
use App\JobApplication\Domain\Repository\IJobApplicationRepository;
use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Persistence\DoctrineCriteriaConverter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

final readonly class DoctrineJobApplicationRepository implements IJobApplicationRepository
{
    private const array PATHS_BY_FIELD = [
        'status' => ['jobApplication.status'],
        'jobPositionHash' => ['jobPosition.hash'],
        'candidate' => ['jobApplication.candidateFullName', 'jobApplication.candidateEmail'],
        'appliedAt' => ['jobApplication.appliedAt'],
    ];

    private DoctrineCriteriaConverter $criteriaConverter;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->criteriaConverter = new DoctrineCriteriaConverter(self::PATHS_BY_FIELD);
    }

    public function save(JobApplication $jobApplication): void
    {
        $this->entityManager->persist($jobApplication);
        $this->entityManager->flush();
    }

    public function findByHash(JobApplicationHash $hash): ?JobApplication
    {
        return $this->repository()->findOneBy(['hash' => $hash]);
    }

    public function search(Criteria $criteria): array
    {
        $queryBuilder = $this->withJobPosition()->addSelect('jobPosition');

        /** @var list<JobApplication> */
        return $this->criteriaConverter->apply($queryBuilder, $criteria)->getQuery()->getResult();
    }

    public function count(Criteria $criteria): int
    {
        $queryBuilder = $this->withJobPosition()->select('COUNT(jobApplication.id)');

        /** @var int|string $total */
        $total = $this->criteriaConverter->applyFilters($queryBuilder, $criteria)->getQuery()->getSingleScalarResult();

        return (int) $total;
    }

    /** Criteria can filter by the job position, so every criteria query joins it. */
    private function withJobPosition(): QueryBuilder
    {
        return $this->repository()->createQueryBuilder('jobApplication')->join('jobApplication.jobPosition', 'jobPosition');
    }

    /** @return EntityRepository<JobApplication> */
    private function repository(): EntityRepository
    {
        return $this->entityManager->getRepository(JobApplication::class);
    }
}
