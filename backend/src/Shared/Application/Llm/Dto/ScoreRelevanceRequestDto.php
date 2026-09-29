<?php

declare(strict_types=1);

namespace App\Shared\Application\Llm\Dto;

final readonly class ScoreRelevanceRequestDto
{
    /** @param list<string> $criteria */
    public function __construct(
        public string $text,
        public array $criteria,
    ) {
    }
}
