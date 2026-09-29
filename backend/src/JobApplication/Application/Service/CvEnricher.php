<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Service;

use App\JobApplication\Application\Dto\CvEnrichmentDto;
use App\JobApplication\Domain\Entity\JobApplication;
use App\Shared\Application\Llm\Dto\ScoreRelevanceRequestDto;
use App\Shared\Application\Llm\Dto\SummarizeRequestDto;
use App\Shared\Application\Llm\ILlmClient;

final readonly class CvEnricher
{
    private const int SUMMARY_MAX_LENGTH = 280;

    public function __construct(private ILlmClient $llmClient)
    {
    }

    public function enrich(JobApplication $jobApplication): CvEnrichmentDto
    {
        return new CvEnrichmentDto(
            $this->llmClient->summarize(new SummarizeRequestDto($jobApplication->cvText, self::SUMMARY_MAX_LENGTH)),
            $this->llmClient->scoreRelevance(new ScoreRelevanceRequestDto($jobApplication->cvText, $jobApplication->jobPosition->requiredSkills)),
        );
    }
}
