<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Http\Controller;

use App\JobApplication\Application\Query\FindJobApplicationQuery;
use App\JobApplication\Infrastructure\Http\ViewModel\JobApplicationDetailViewModel;
use App\Shared\Application\Bus\Query\IQueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class FindJobApplicationController extends AbstractController
{
    public function __construct(private readonly IQueryBus $queryBus)
    {
    }

    #[Route('/api/v1/job-applications/{hash}', requirements: ['hash' => Requirement::UUID], methods: ['GET'])]
    public function __invoke(string $hash): JsonResponse
    {
        $jobApplication = $this->queryBus->ask(new FindJobApplicationQuery($hash));

        return $this->json(JobApplicationDetailViewModel::fromDto($jobApplication));
    }
}
