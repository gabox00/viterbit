<?php

declare(strict_types=1);

namespace App\Tests\Double;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Transport\TransportInterface;

/** Simulates a broker that is down: every send fails. */
final class UnavailableTransport implements TransportInterface
{
    public function send(Envelope $envelope): Envelope
    {
        throw new TransportException('The broker is unavailable.');
    }

    public function get(): iterable
    {
        return [];
    }

    public function ack(Envelope $envelope): void
    {
    }

    public function reject(Envelope $envelope): void
    {
    }
}
