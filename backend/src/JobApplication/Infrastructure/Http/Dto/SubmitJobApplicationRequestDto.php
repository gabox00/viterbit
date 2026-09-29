<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Http\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class SubmitJobApplicationRequestDto
{
    public function __construct(
        #[Assert\NotBlank, Assert\Uuid]
        public string $jobPositionHash = '',
        #[Assert\NotBlank, Assert\Length(max: 150)]
        public string $candidateFullName = '',
        #[Assert\NotBlank, Assert\Email, Assert\Length(max: 180)]
        public string $candidateEmail = '',
        #[Assert\Length(max: 30), Assert\Regex('/^\+?[0-9 ()-]{6,30}$/', message: 'This value is not a valid phone number.')]
        public ?string $candidatePhone = null,
        #[Assert\Length(max: 2000)]
        public ?string $notes = null,
        #[Assert\NotBlank, Assert\Length(min: 50, max: 20000)]
        public string $cvText = '',
    ) {
    }
}
