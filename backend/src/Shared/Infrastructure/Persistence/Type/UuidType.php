<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Type;

use App\Shared\Domain\ValueObject\Uuid;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\StringType;

/** @template T of Uuid */
abstract class UuidType extends StringType
{
    /** @return class-string<T> */
    abstract protected function valueObjectClass(): string;

    /** @return T|null */
    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Uuid
    {
        return is_string($value) ? new ($this->valueObjectClass())($value) : null;
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        return match (true) {
            null === $value, is_string($value) => $value,
            $value instanceof Uuid => $value->value,
            default => throw InvalidType::new($value, static::class, ['null', 'string', Uuid::class]),
        };
    }
}
