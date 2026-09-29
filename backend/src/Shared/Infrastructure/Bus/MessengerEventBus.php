<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Domain\Bus\Event\IDomainEvent;
use App\Shared\Domain\Bus\Event\IEventBus;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessengerEventBus implements IEventBus
{
    public function __construct(
        #[Autowire(service: 'event.bus')]
        private MessageBusInterface $eventBus,
    ) {
    }

    public function publish(IDomainEvent ...$domainEvents): void
    {
        foreach ($domainEvents as $domainEvent) {
            $this->eventBus->dispatch($domainEvent, [
                new AmqpStamp(attributes: ['type' => $domainEvent::eventName()]),
            ]);
        }
    }
}
