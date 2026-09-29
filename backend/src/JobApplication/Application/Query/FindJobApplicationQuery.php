<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Query;

use App\JobApplication\Application\Dto\JobApplicationDetailDto;
use App\Shared\Application\Bus\Query\IQuery;

/** @implements IQuery<JobApplicationDetailDto> */
final readonly class FindJobApplicationQuery implements IQuery
{
    public function __construct(public string $hash)
    {
    }
}
