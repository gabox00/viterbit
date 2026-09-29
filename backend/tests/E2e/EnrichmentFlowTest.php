<?php

declare(strict_types=1);

namespace App\Tests\E2e;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

final class EnrichmentFlowTest extends ApiTestCase
{
    public function testSubmittedApplicationsEndUpEnrichedWithSummaryAndScore(): void
    {
        $hash = $this->submitApplication();

        $this->consume('enrich_cv_on_job_application_submitted');

        $detail = $this->getJson('/api/v1/job-applications/'.$hash);
        self::assertSame('enriched', $detail['status']);
        self::assertIsString($detail['ai_summary']);
        self::assertStringStartsWith('Senior PHP developer', $detail['ai_summary']);
        self::assertSame(71, $detail['ai_score']);
        self::assertIsString($detail['enriched_at']);

        $page = $this->getJson('/api/v1/job-applications?status=enriched');
        self::assertSame(1, $page['total']);
    }

    private function consume(string $transportName): void
    {
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.'.$transportName);
        /** @var MessageBusInterface $eventBus */
        $eventBus = self::getContainer()->get('event.bus');

        $envelopes = [...$transport->get()];
        self::assertNotEmpty($envelopes, sprintf('Expected messages in "%s".', $transportName));

        foreach ($envelopes as $envelope) {
            $eventBus->dispatch($envelope->with(new ReceivedStamp($transportName)));
            $transport->ack($envelope);
        }
    }
}
