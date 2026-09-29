<?php

declare(strict_types=1);

namespace App\JobPosition\Infrastructure\Http\Controller;

use App\JobPosition\Application\Query\FindJobPositionQuery;
use App\JobPosition\Infrastructure\Http\ViewModel\JobPositionViewModel;
use App\Shared\Application\Bus\Query\IQueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class FindJobPositionController extends AbstractController
{
    public function __construct(private readonly IQueryBus $queryBus)
    {
    }

    #[Route('/api/v1/job-positions/{hash}', requirements: ['hash' => Requirement::UUID], methods: ['GET'])]
    public function __invoke(string $hash): JsonResponse
    {
        $jobPosition = $this->queryBus->ask(new FindJobPositionQuery($hash));

        return $this->json(JobPositionViewModel::fromDto($jobPosition));
    }
}
