<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Persistence\Type;

use App\JobApplication\Domain\ValueObject\AiScore;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;

final class AiScoreType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?AiScore
    {
        return is_int($value) || is_numeric($value) ? new AiScore((int) $value) : null;
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?int
    {
        return match (true) {
            null === $value => null,
            $value instanceof AiScore => $value->value,
            default => throw InvalidType::new($value, self::class, ['null', AiScore::class]),
        };
    }

    #[\Override]
    public function getBindingType(): ParameterType
    {
        return ParameterType::INTEGER;
    }
}
