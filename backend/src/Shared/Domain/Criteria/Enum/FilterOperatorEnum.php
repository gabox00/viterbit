<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria\Enum;

enum FilterOperatorEnum: string
{
    case Equal = 'equal';
    case Contains = 'contains';
}
