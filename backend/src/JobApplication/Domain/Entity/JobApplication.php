<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Entity;

use App\JobApplication\Domain\Enum\JobApplicationStatusEnum;
use App\JobApplication\Domain\Event\JobApplicationSubmitted;
use App\JobApplication\Domain\ValueObject\AiScore;
use App\JobApplication\Domain\ValueObject\Email;
use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\JobPosition\Domain\Entity\JobPosition;
use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\Exception\InvalidValueException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'job_application')]
final class JobApplication extends AggregateRoot
{
    public function __construct(
        #[ORM\Column(type: 'job_application_hash', unique: true)]
        public readonly JobApplicationHash $hash,
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        public readonly JobPosition $jobPosition,
        #[ORM\Column]
        public readonly string $candidateFullName,
        #[ORM\Column(type: 'email')]
        public readonly Email $candidateEmail,
        #[ORM\Column(nullable: true)]
        public readonly ?string $candidatePhone,
        #[ORM\Column(type: 'text', nullable: true)]
        public readonly ?string $notes,
        #[ORM\Column(type: 'text')]
        public readonly string $cvText,
        #[ORM\Column(enumType: JobApplicationStatusEnum::class)]
        public private(set) JobApplicationStatusEnum $status,
        #[ORM\Column(type: 'text', nullable: true)]
        public private(set) ?string $aiSummary,
        #[ORM\Column(type: 'ai_score', nullable: true)]
        public private(set) ?AiScore $aiScore,
        #[ORM\Column(type: 'utc_datetime_immutable')]
        public readonly \DateTimeImmutable $appliedAt,
        #[ORM\Column(type: 'utc_datetime_immutable', nullable: true)]
        public private(set) ?\DateTimeImmutable $enrichedAt,
        #[ORM\Id, ORM\GeneratedValue, ORM\Column]
        public private(set) ?int $id = null,
    ) {
    }

    public static function submit(
        JobApplicationHash $hash,
        JobPosition $jobPosition,
        string $candidateFullName,
        Email $candidateEmail,
        ?string $candidatePhone,
        ?string $notes,
        string $cvText,
        \DateTimeImmutable $appliedAt,
    ): self {
        self::assertNotBlank('Candidate full name', $candidateFullName);
        self::assertNotBlank('CV text', $cvText);

        $jobApplication = new self(
            $hash,
            $jobPosition,
            trim($candidateFullName),
            $candidateEmail,
            $candidatePhone,
            $notes,
            trim($cvText),
            JobApplicationStatusEnum::Received,
            null,
            null,
            $appliedAt,
            null,
        );

        $jobApplication->record(new JobApplicationSubmitted($hash->value, $jobPosition->hash->value, $jobApplication->cvText));

        return $jobApplication;
    }

    public function attachEnrichment(string $aiSummary, AiScore $aiScore, \DateTimeImmutable $enrichedAt): void
    {
        $this->status = JobApplicationStatusEnum::Enriched;
        $this->aiSummary = $aiSummary;
        $this->aiScore = $aiScore;
        $this->enrichedAt = $enrichedAt;
    }

    private static function assertNotBlank(string $fieldName, string $value): void
    {
        if ('' === trim($value)) {
            throw new InvalidValueException(sprintf('%s cannot be blank.', $fieldName));
        }
    }
}
