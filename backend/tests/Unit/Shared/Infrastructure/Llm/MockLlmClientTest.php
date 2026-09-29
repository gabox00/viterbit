<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Infrastructure\Llm;

use App\Shared\Application\Llm\Dto\ScoreRelevanceRequestDto;
use App\Shared\Application\Llm\Dto\SummarizeRequestDto;
use App\Shared\Infrastructure\Llm\Mock\MockLlmClient;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MockLlmClientTest extends TestCase
{
    private MockLlmClient $llmClient;

    protected function setUp(): void
    {
        $this->llmClient = new MockLlmClient(0);
    }

    public function testKeepsShortTextsWithNormalizedWhitespace(): void
    {
        self::assertSame(
            'PHP developer. Loves tests.',
            $this->llmClient->summarize(new SummarizeRequestDto("  PHP developer.\n\n Loves   tests. ", 280)),
        );
    }

    public function testSummarizesLongTextsWithTheWholeSentencesThatFit(): void
    {
        $text = 'First sentence here. Second sentence here. Third sentence is too long to fit.';

        self::assertSame('First sentence here. Second sentence here.', $this->llmClient->summarize(new SummarizeRequestDto($text, 50)));
    }

    public function testTruncatesWhenTheFirstSentenceDoesNotFit(): void
    {
        self::assertSame(
            'An extremely long f…',
            $this->llmClient->summarize(new SummarizeRequestDto('An extremely long first sentence without any break', 20)),
        );
    }

    public function testDropsTrailingWhitespaceBeforeTheEllipsis(): void
    {
        self::assertSame(
            'An extremely long…',
            $this->llmClient->summarize(new SummarizeRequestDto('An extremely long first sentence without any break', 19)),
        );
    }

    public function testCountsCharactersNotBytes(): void
    {
        self::assertSame('Ñandú. Ñandú.', $this->llmClient->summarize(new SummarizeRequestDto('Ñandú. Ñandú. Árbol grande.', 13)));
        self::assertSame('Ñandúes c…', $this->llmClient->summarize(new SummarizeRequestDto('Ñandúes corren muy rápido sin parar', 10)));
    }

    public function testIncludesASentenceThatEndsExactlyAtTheLimit(): void
    {
        self::assertSame('First one. Second.', $this->llmClient->summarize(new SummarizeRequestDto('First one. Second. Third.', 18)));
    }

    public function testSimulatesTheConfiguredLatencyOnEveryCall(): void
    {
        $llmClient = new MockLlmClient(20);

        $startedAt = hrtime(true);
        $llmClient->summarize(new SummarizeRequestDto('CV.', 280));
        $llmClient->scoreRelevance(new ScoreRelevanceRequestDto('CV.', ['PHP']));
        $elapsedMs = (hrtime(true) - $startedAt) / 1_000_000;

        self::assertGreaterThanOrEqual(40, $elapsedMs);
    }

    /** @return iterable<string, array{string, list<string>, int}> */
    public static function relevanceCases(): iterable
    {
        yield 'no criteria' => ['PHP developer', [], 0];
        yield 'no matches' => ['Pastry chef', ['PHP', 'Symfony'], 0];
        yield 'all matches, case insensitive' => ['php and SYMFONY', ['PHP', 'Symfony'], 100];
        yield 'rounded down partial match' => ['PHP only', ['PHP', 'Symfony', 'Docker'], 33];
        yield 'rounded up partial match' => ['PHP and Docker', ['PHP', 'Symfony', 'Docker'], 67];
        yield 'whole words only' => ['Google Cloud', ['Go'], 0];
        yield 'symbols in skills' => ['Strong C++ and CI/CD background', ['C++', 'CI/CD'], 100];
    }

    /** @param list<string> $criteria */
    #[DataProvider('relevanceCases')]
    public function testScoresRelevanceAsThePercentageOfMentionedCriteria(string $text, array $criteria, int $expectedScore): void
    {
        self::assertSame($expectedScore, $this->llmClient->scoreRelevance(new ScoreRelevanceRequestDto($text, $criteria)));
    }
}
