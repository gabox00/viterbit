<?php

declare(strict_types=1);

namespace App\Tests\Unit\JobApplication\Domain;

use App\JobApplication\Domain\ValueObject\AiScore;
use App\Shared\Domain\Exception\InvalidValueException;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class AiScoreTest extends TestCase
{
    #[TestWith([0])]
    #[TestWith([100])]
    public function testAcceptsBoundaries(int $score): void
    {
        self::assertSame($score, new AiScore($score)->value);
    }

    #[TestWith([-1])]
    #[TestWith([101])]
    public function testRejectsOutOfRangeScores(int $score): void
    {
        $this->expectException(InvalidValueException::class);

        new AiScore($score);
    }
}
