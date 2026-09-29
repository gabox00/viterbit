<?php

declare(strict_types=1);

namespace App\JobPosition\Application\Dto;

use App\JobPosition\Domain\Entity\JobPosition;

final readonly class JobPositionDto
{
    /** @param list<string> $requiredSkills */
    public function __construct(
        public string $hash,
        public string $title,
        public string $description,
        public array $requiredSkills,
    ) {
    }

    public static function fromJobPosition(JobPosition $jobPosition): self
    {
        return new self($jobPosition->hash->value, $jobPosition->title, $jobPosition->description, $jobPosition->requiredSkills);
    }
}
