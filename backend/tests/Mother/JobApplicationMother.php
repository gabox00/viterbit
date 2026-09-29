<?php

declare(strict_types=1);

namespace App\Tests\Mother;

use App\JobApplication\Domain\Entity\JobApplication;
use App\JobApplication\Domain\ValueObject\Email;
use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\JobPosition\Domain\Entity\JobPosition;
use Symfony\Component\Uid\Uuid;

final class JobApplicationMother
{
    public const string BACKEND_JOB_POSITION_HASH = '0199a0e0-0000-7000-8000-000000000001';
    public const string FRONTEND_JOB_POSITION_HASH = '0199a0e0-0000-7000-8000-000000000002';
    public const string CV_TEXT = 'Senior PHP developer with eight years of experience building Symfony applications. Worked with DDD, RabbitMQ and Docker in production.';

    public static function submitted(
        ?string $hash = null,
        ?JobPosition $jobPosition = null,
        string $candidateFullName = 'Ada Lovelace',
        string $candidateEmail = 'ada@example.com',
        string $appliedAt = '2026-09-28 10:00:00',
    ): JobApplication {
        return JobApplication::submit(
            new JobApplicationHash($hash ?? Uuid::v7()->toRfc4122()),
            $jobPosition ?? JobPositionMother::backend(),
            $candidateFullName,
            new Email($candidateEmail),
            '+34 600 000 000',
            'Available immediately.',
            self::CV_TEXT,
            new \DateTimeImmutable($appliedAt, new \DateTimeZone('UTC')),
        );
    }
}
