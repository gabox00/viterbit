<?php

declare(strict_types=1);

namespace App\Tests\Double;

use App\Shared\Domain\Bus\Event\IDomainEvent;
use App\Shared\Domain\Bus\Event\IEventBus;

final class SpyEventBus implements IEventBus
{
    /** @var list<IDomainEvent> */
    public private(set) array $publishedEvents = [];

    public function publish(IDomainEvent ...$domainEvents): void
    {
        foreach ($domainEvents as $domainEvent) {
            $this->publishedEvents[] = $domainEvent;
        }
    }
}
