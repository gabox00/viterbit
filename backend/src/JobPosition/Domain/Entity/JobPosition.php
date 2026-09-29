<?php

declare(strict_types=1);

namespace App\JobPosition\Domain\Entity;

use App\JobPosition\Domain\ValueObject\JobPositionHash;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'job_position')]
final readonly class JobPosition
{
    /** @param list<string> $requiredSkills */
    public function __construct(
        #[ORM\Column(type: 'job_position_hash', unique: true)]
        public JobPositionHash $hash,
        #[ORM\Column]
        public string $title,
        #[ORM\Column(type: 'text')]
        public string $description,
        #[ORM\Column(type: 'json')]
        public array $requiredSkills,
        #[ORM\Id, ORM\GeneratedValue, ORM\Column]
        public ?int $id = null,
    ) {
    }
}
