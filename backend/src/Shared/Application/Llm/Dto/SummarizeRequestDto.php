<?php

declare(strict_types=1);

namespace App\Shared\Application\Llm\Dto;

final readonly class SummarizeRequestDto
{
    public function __construct(
        public string $text,
        public int $maxLength,
    ) {
    }
}
