<?php

declare(strict_types=1);

namespace App\JobPosition\Application\Query;

use App\JobPosition\Application\Dto\JobPositionDto;
use App\Shared\Application\Bus\Query\IQuery;

/** @implements IQuery<JobPositionDto> */
final readonly class FindJobPositionQuery implements IQuery
{
    public function __construct(public string $hash)
    {
    }
}
