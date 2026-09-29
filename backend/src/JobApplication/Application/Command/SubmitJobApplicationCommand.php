<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Command;

use App\Shared\Application\Bus\Command\ICommand;

final readonly class SubmitJobApplicationCommand implements ICommand
{
    public function __construct(
        public string $hash,
        public string $jobPositionHash,
        public string $candidateFullName,
        public string $candidateEmail,
        public ?string $candidatePhone,
        public ?string $notes,
        public string $cvText,
    ) {
    }
}
