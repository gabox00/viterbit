<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use App\Shared\Domain\Bus\Event\IDomainEvent;

abstract class AggregateRoot
{
    /** @var list<IDomainEvent> */
    private array $domainEvents = [];

    /** @return list<IDomainEvent> */
    final public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }

    final protected function record(IDomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }
}
