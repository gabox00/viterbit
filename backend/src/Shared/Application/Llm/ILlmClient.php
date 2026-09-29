<?php

declare(strict_types=1);

namespace App\Shared\Application\Llm;

use App\Shared\Application\Llm\Dto\ScoreRelevanceRequestDto;
use App\Shared\Application\Llm\Dto\SummarizeRequestDto;

interface ILlmClient
{
    public function summarize(SummarizeRequestDto $request): string;

    public function scoreRelevance(ScoreRelevanceRequestDto $request): int;
}
