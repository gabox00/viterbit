<?php

declare(strict_types=1);

namespace App\Tests\Integration\Shared;

use App\JobApplication\Domain\Event\JobApplicationSubmitted;
use App\Shared\Domain\Bus\Event\IDomainEvent;
use App\Shared\Domain\Bus\Event\IEventBus;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

final class MessengerEventBusRoutingTest extends KernelTestCase
{
    /** @return iterable<string, array{IDomainEvent, string}> */
    public static function events(): iterable
    {
        yield 'job application submitted' => [
            new JobApplicationSubmitted('0199a0e0-1111-7000-8000-000000000001', '0199a0e0-0000-7000-8000-000000000001', 'CV'),
            'enrich_cv_on_job_application_submitted',
        ];
    }

    #[DataProvider('events')]
    public function testRoutesEachEventToTheTransportNamedAfterItsQueue(IDomainEvent $event, string $transportName): void
    {
        /** @var IEventBus $eventBus */
        $eventBus = self::getContainer()->get(IEventBus::class);
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.'.$transportName);

        $eventBus->publish($event);

        $envelopes = $transport->getSent();
        self::assertCount(1, $envelopes);
        self::assertEquals($event, $envelopes[0]->getMessage());
        self::assertSame($event::eventName(), $envelopes[0]->last(AmqpStamp::class)?->getAttributes()['type']);
    }
}
