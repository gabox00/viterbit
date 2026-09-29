<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Llm;

use App\Shared\Application\Llm\ILlmClient;
use App\Shared\Infrastructure\Llm\Enum\LlmProviderEnum;
use App\Shared\Infrastructure\Llm\Mock\MockLlmClient;

final class LlmClientBuilder
{
    private LlmProviderEnum $provider = LlmProviderEnum::Mock;
    private int $simulatedLatencyMs = 0;

    public function withProvider(LlmProviderEnum $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function withSimulatedLatencyMs(int $simulatedLatencyMs): self
    {
        $this->simulatedLatencyMs = $simulatedLatencyMs;

        return $this;
    }

    public function build(): ILlmClient
    {
        return match ($this->provider) {
            LlmProviderEnum::Mock => new MockLlmClient($this->simulatedLatencyMs),
        };
    }
}
