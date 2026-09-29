<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Http\ViewModel;

final readonly class JobApplicationSubmittedViewModel
{
    public function __construct(public string $hash)
    {
    }
}
