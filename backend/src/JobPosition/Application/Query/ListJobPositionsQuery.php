<?php

declare(strict_types=1);

namespace App\JobPosition\Application\Query;

use App\JobPosition\Application\Dto\JobPositionDto;
use App\Shared\Application\Bus\Query\IQuery;

/** @implements IQuery<list<JobPositionDto>> */
final readonly class ListJobPositionsQuery implements IQuery
{
}
