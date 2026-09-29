<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\Enum\OrderDirectionEnum;

final readonly class Order
{
    public function __construct(
        public string $field,
        public OrderDirectionEnum $direction,
    ) {
    }

    public static function desc(string $field): self
    {
        return new self($field, OrderDirectionEnum::Desc);
    }
}
