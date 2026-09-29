<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain;

use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\Shared\Domain\Exception\InvalidValueException;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class UuidTest extends TestCase
{
    public function testComparesByValue(): void
    {
        $hash = new JobApplicationHash('0199a0e0-1111-7000-8000-000000000001');

        self::assertTrue($hash->equals(new JobApplicationHash('0199a0e0-1111-7000-8000-000000000001')));
        self::assertFalse($hash->equals(new JobApplicationHash('0199a0e0-1111-7000-8000-000000000002')));
    }

    #[TestWith(['not-a-uuid'])]
    #[TestWith(['0199A0E0-1111-7000-8000-000000000001'])]
    #[TestWith([' 0199a0e0-1111-7000-8000-000000000001'])]
    public function testRejectsInvalidUuids(string $invalidUuid): void
    {
        $this->expectException(InvalidValueException::class);

        new JobApplicationHash($invalidUuid);
    }
}
