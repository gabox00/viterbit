<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;

/** Stores dates in UTC with microseconds (TIMESTAMP(6)), so ordering by date keeps sub-second precision. */
final class UtcDateTimeImmutableType extends Type
{
    private const string NAME = 'utc_datetime_immutable';
    private const string FORMAT = 'Y-m-d H:i:s.u';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTimeTypeDeclarationSQL($column);
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?\DateTimeImmutable
    {
        if (!is_string($value)) {
            return null;
        }

        // PostgreSQL omits the fraction when the microseconds are zero, so parse leniently instead of with FORMAT
        try {
            return new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        } catch (\Exception $exception) {
            throw InvalidFormat::new($value, self::NAME, self::FORMAT, $exception);
        }
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        return match (true) {
            null === $value => null,
            $value instanceof \DateTimeImmutable => $value->setTimezone(new \DateTimeZone('UTC'))->format(self::FORMAT),
            default => throw InvalidType::new($value, self::NAME, ['null', \DateTimeImmutable::class]),
        };
    }
}
