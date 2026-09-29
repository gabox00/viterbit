<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

final readonly class Criteria
{
    /** @param list<Filter> $filters */
    public function __construct(
        public array $filters = [],
        public ?Order $order = null,
        public ?int $limit = null,
        public ?int $offset = null,
    ) {
    }
}
