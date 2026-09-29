<?php

declare(strict_types=1);

namespace App\JobPosition\Infrastructure\Http\Controller;

use App\JobPosition\Application\Query\ListJobPositionsQuery;
use App\JobPosition\Infrastructure\Http\ViewModel\JobPositionViewModel;
use App\Shared\Application\Bus\Query\IQueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ListJobPositionsController extends AbstractController
{
    public function __construct(private readonly IQueryBus $queryBus)
    {
    }

    #[Route('/api/v1/job-positions', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $jobPositions = $this->queryBus->ask(new ListJobPositionsQuery());

        return $this->json(array_map(JobPositionViewModel::fromDto(...), $jobPositions));
    }
}
