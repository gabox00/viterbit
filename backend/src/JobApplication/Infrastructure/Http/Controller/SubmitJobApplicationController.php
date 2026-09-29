<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Http\Controller;

use App\JobApplication\Application\Command\SubmitJobApplicationCommand;
use App\JobApplication\Infrastructure\Http\Dto\SubmitJobApplicationRequestDto;
use App\JobApplication\Infrastructure\Http\ViewModel\JobApplicationSubmittedViewModel;
use App\Shared\Application\Bus\Command\ICommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

final class SubmitJobApplicationController extends AbstractController
{
    public function __construct(private readonly ICommandBus $commandBus)
    {
    }

    #[Route('/api/v1/job-applications', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] SubmitJobApplicationRequestDto $request): JsonResponse
    {
        $hash = Uuid::v7()->toRfc4122();

        $this->commandBus->dispatch(new SubmitJobApplicationCommand(
            $hash,
            $request->jobPositionHash,
            $request->candidateFullName,
            $request->candidateEmail,
            $this->nullIfBlank($request->candidatePhone),
            $this->nullIfBlank($request->notes),
            $request->cvText,
        ));

        return $this->json(new JobApplicationSubmittedViewModel($hash), Response::HTTP_CREATED);
    }

    private function nullIfBlank(?string $value): ?string
    {
        return null === $value || '' === trim($value) ? null : trim($value);
    }
}
