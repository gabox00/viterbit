<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Http\Dto;

use App\JobApplication\Domain\Enum\JobApplicationStatusEnum;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchJobApplicationsRequestDto
{
    public function __construct(
        #[Assert\Choice(callback: [self::class, 'statuses'])]
        public ?string $status = null,
        #[Assert\Uuid]
        public ?string $jobPositionHash = null,
        #[Assert\Length(max: 100)]
        public ?string $search = null,
        #[Assert\Positive]
        public int $page = 1,
        #[Assert\Range(min: 1, max: 100)]
        public int $perPage = 20,
    ) {
    }

    /** @return list<string> */
    public static function statuses(): array
    {
        return array_column(JobApplicationStatusEnum::cases(), 'value');
    }
}
