<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\Enum\FilterOperatorEnum;

final readonly class Filter
{
    public function __construct(
        public string $field,
        public FilterOperatorEnum $operator,
        public string $value,
    ) {
    }

    public static function equal(string $field, string $value): self
    {
        return new self($field, FilterOperatorEnum::Equal, $value);
    }

    public static function contains(string $field, string $value): self
    {
        return new self($field, FilterOperatorEnum::Contains, $value);
    }
}
