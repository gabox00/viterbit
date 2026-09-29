<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Event;

interface IDomainEvent
{
    public static function eventName(): string;
}
