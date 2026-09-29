<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Event;

interface IEventBus
{
    public function publish(IDomainEvent ...$domainEvents): void;
}
