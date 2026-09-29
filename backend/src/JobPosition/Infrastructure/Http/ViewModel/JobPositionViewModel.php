<?php

declare(strict_types=1);

namespace App\JobPosition\Infrastructure\Http\ViewModel;

use App\JobPosition\Application\Dto\JobPositionDto;

final readonly class JobPositionViewModel
{
    /** @param list<string> $requiredSkills */
    public function __construct(
        public string $hash,
        public string $title,
        public string $description,
        public array $requiredSkills,
    ) {
    }

    public static function fromDto(JobPositionDto $jobPosition): self
    {
        return new self($jobPosition->hash, $jobPosition->title, $jobPosition->description, $jobPosition->requiredSkills);
    }
}
