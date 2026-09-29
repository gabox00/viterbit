<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Persistence\Type;

use App\JobApplication\Domain\ValueObject\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\StringType;

final class EmailType extends StringType
{
    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Email
    {
        return is_string($value) ? new Email($value) : null;
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        return match (true) {
            null === $value, is_string($value) => $value,
            $value instanceof Email => $value->value,
            default => throw InvalidType::new($value, self::class, ['null', 'string', Email::class]),
        };
    }
}
