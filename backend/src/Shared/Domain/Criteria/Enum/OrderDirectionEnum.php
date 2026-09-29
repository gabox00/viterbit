<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria\Enum;

enum OrderDirectionEnum: string
{
    case Asc = 'ASC';
    case Desc = 'DESC';
}
