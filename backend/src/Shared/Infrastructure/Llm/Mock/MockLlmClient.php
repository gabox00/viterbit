<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Llm\Mock;

use App\Shared\Application\Llm\Dto\ScoreRelevanceRequestDto;
use App\Shared\Application\Llm\Dto\SummarizeRequestDto;
use App\Shared\Application\Llm\ILlmClient;

final readonly class MockLlmClient implements ILlmClient
{
    private const string ELLIPSIS = '…';

    public function __construct(private int $simulatedLatencyMs)
    {
    }

    public function summarize(SummarizeRequestDto $request): string
    {
        $this->simulateLatency();

        $text = $this->normalizeWhitespace($request->text);

        $summary = '';
        foreach ($this->splitSentences($text) as $sentence) {
            $candidate = '' === $summary ? $sentence : $summary.' '.$sentence;
            if (mb_strlen($candidate) > $request->maxLength) {
                break;
            }
            $summary = $candidate;
        }

        if ('' === $summary) {
            return rtrim(mb_substr($text, 0, $request->maxLength - 1)).self::ELLIPSIS;
        }

        return $summary;
    }

    public function scoreRelevance(ScoreRelevanceRequestDto $request): int
    {
        $this->simulateLatency();

        if ([] === $request->criteria) {
            return 0;
        }

        $matchedCriteria = array_filter(
            $request->criteria,
            fn (string $criterion): bool => $this->mentions($request->text, $criterion),
        );

        return (int) round(count($matchedCriteria) * 100 / count($request->criteria));
    }

    private function mentions(string $text, string $term): bool
    {
        return 1 === preg_match('/(?<!\w)'.preg_quote($term, '/').'(?!\w)/iu', $text);
    }

    private function normalizeWhitespace(string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }

    /** @return list<string> */
    private function splitSentences(string $text): array
    {
        return preg_split('/(?<=[.!?])\s+/u', $text, flags: PREG_SPLIT_NO_EMPTY) ?: [];
    }

    private function simulateLatency(): void
    {
        if ($this->simulatedLatencyMs > 0) {
            usleep($this->simulatedLatencyMs * 1000);
        }
    }
}
