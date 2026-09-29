<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Infrastructure\Llm;

use App\Shared\Infrastructure\Llm\Enum\LlmProviderEnum;
use App\Shared\Infrastructure\Llm\LlmClientBuilder;
use App\Shared\Infrastructure\Llm\Mock\MockLlmClient;
use PHPUnit\Framework\TestCase;

final class LlmClientBuilderTest extends TestCase
{
    public function testBuildsTheClientOfTheSelectedProvider(): void
    {
        $llmClient = new LlmClientBuilder()
            ->withProvider(LlmProviderEnum::Mock)
            ->withSimulatedLatencyMs(0)
            ->build();

        self::assertInstanceOf(MockLlmClient::class, $llmClient);
    }
}
