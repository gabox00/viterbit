<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Enum;

enum JobApplicationStatusEnum: string
{
    case Received = 'received';
    case Enriched = 'enriched';
}
