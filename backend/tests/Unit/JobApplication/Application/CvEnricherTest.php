<?php

declare(strict_types=1);

namespace App\Tests\Unit\JobApplication\Application;

use App\JobApplication\Application\Service\CvEnricher;
use App\Shared\Application\Llm\Dto\ScoreRelevanceRequestDto;
use App\Shared\Application\Llm\Dto\SummarizeRequestDto;
use App\Shared\Application\Llm\ILlmClient;
use App\Tests\Mother\JobApplicationMother;
use PHPUnit\Framework\TestCase;

final class CvEnricherTest extends TestCase
{
    public function testAsksTheLlmForASummaryAndARelevanceScoreAgainstTheJobPositionSkills(): void
    {
        $jobApplication = JobApplicationMother::submitted();

        $llmClient = $this->createMock(ILlmClient::class);
        $llmClient->expects(self::once())->method('summarize')
            ->with(new SummarizeRequestDto(JobApplicationMother::CV_TEXT, 280))
            ->willReturn('The summary.');
        $llmClient->expects(self::once())->method('scoreRelevance')
            ->with(new ScoreRelevanceRequestDto(JobApplicationMother::CV_TEXT, $jobApplication->jobPosition->requiredSkills))
            ->willReturn(50);

        $enrichment = new CvEnricher($llmClient)->enrich($jobApplication);

        self::assertSame('The summary.', $enrichment->summary);
        self::assertSame(50, $enrichment->score);
    }
}
