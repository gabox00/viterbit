<?php

declare(strict_types=1);

namespace App\Tests\Unit\JobApplication\Domain;

use App\JobApplication\Domain\ValueObject\Email;
use App\Shared\Domain\Exception\InvalidValueException;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function testNormalizesCaseAndWhitespace(): void
    {
        self::assertSame('ada@example.com', new Email('  Ada@Example.COM ')->value);
    }

    #[TestWith([''])]
    #[TestWith(['not-an-email'])]
    #[TestWith(['ada@'])]
    public function testRejectsInvalidEmails(string $invalidEmail): void
    {
        $this->expectException(InvalidValueException::class);

        new Email($invalidEmail);
    }
}
