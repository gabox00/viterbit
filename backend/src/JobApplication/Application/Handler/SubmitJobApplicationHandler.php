<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Handler;

use App\JobApplication\Application\Command\SubmitJobApplicationCommand;
use App\JobApplication\Application\Exception\UnknownJobPositionException;
use App\JobApplication\Domain\Entity\JobApplication;
use App\JobApplication\Domain\Repository\IJobApplicationRepository;
use App\JobApplication\Domain\ValueObject\Email;
use App\JobApplication\Domain\ValueObject\JobApplicationHash;
use App\JobPosition\Domain\Repository\IJobPositionRepository;
use App\JobPosition\Domain\ValueObject\JobPositionHash;
use App\Shared\Application\Bus\Command\ICommandHandler;
use App\Shared\Domain\Bus\Event\IEventBus;
use Psr\Clock\ClockInterface;

final readonly class SubmitJobApplicationHandler implements ICommandHandler
{
    public function __construct(
        private IJobApplicationRepository $jobApplicationRepository,
        private IJobPositionRepository $jobPositionRepository,
        private IEventBus $eventBus,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(SubmitJobApplicationCommand $command): void
    {
        $jobPositionHash = new JobPositionHash($command->jobPositionHash);
        $jobPosition = $this->jobPositionRepository->findByHash($jobPositionHash)
            ?? throw UnknownJobPositionException::withHash($jobPositionHash);

        $jobApplication = JobApplication::submit(
            new JobApplicationHash($command->hash),
            $jobPosition,
            $command->candidateFullName,
            new Email($command->candidateEmail),
            $command->candidatePhone,
            $command->notes,
            $command->cvText,
            $this->clock->now(),
        );

        // Esto se ejecuta dentro de una transacción para evitar problemas de si está caída la cola y demás
        // hago esto porque es lo más práctico para este ejercicio
        // se puede hacer un comando programado que busca aplicaciones received de hace más de N minutos y republica
        // o Los eventos se guardan en una tabla outbox en la misma transacción que la aplicación y un relay los publica en la cola
        $this->jobApplicationRepository->save($jobApplication);
        $this->eventBus->publish(...$jobApplication->pullDomainEvents());
    }
}
