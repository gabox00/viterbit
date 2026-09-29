<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Event;

use App\Shared\Domain\Bus\Event\IDomainEvent;

final readonly class JobApplicationSubmitted implements IDomainEvent
{
    public function __construct(
        public string $jobApplicationHash,
        public string $jobPositionHash,
        public string $cvText,
    ) {
    }

    public static function eventName(): string
    {
        return 'viterbit.job_application.1.event.job_application.submitted';
    }
}
