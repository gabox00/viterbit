<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Http\Controller;

use App\JobApplication\Application\Query\SearchJobApplicationsQuery;
use App\JobApplication\Infrastructure\Http\Dto\SearchJobApplicationsRequestDto;
use App\JobApplication\Infrastructure\Http\ViewModel\JobApplicationPageViewModel;
use App\Shared\Application\Bus\Query\IQueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

final class SearchJobApplicationsController extends AbstractController
{
    public function __construct(private readonly IQueryBus $queryBus)
    {
    }

    #[Route('/api/v1/job-applications', methods: ['GET'])]
    public function __invoke(#[MapQueryString] SearchJobApplicationsRequestDto $request = new SearchJobApplicationsRequestDto()): JsonResponse
    {
        $page = $this->queryBus->ask(new SearchJobApplicationsQuery(
            $request->status ?: null,
            $request->jobPositionHash ?: null,
            $request->search,
            $request->page,
            $request->perPage,
        ));

        return $this->json(JobApplicationPageViewModel::fromDto($page));
    }
}
