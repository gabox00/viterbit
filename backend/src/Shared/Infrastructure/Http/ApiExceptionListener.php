<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Domain\Exception\InvalidValueException;
use App\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener]
final readonly class ApiExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $validationFailure = $exception->getPrevious();

        $response = match (true) {
            $exception instanceof NotFoundException => $this->error($exception->getMessage(), Response::HTTP_NOT_FOUND),
            $exception instanceof InvalidValueException => $this->error($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY),
            $validationFailure instanceof ValidationFailedException => $this->validationErrors($validationFailure->getViolations()),
            default => null,
        };

        if (null !== $response) {
            $event->setResponse($response);
        }
    }

    private function error(string $message, int $status): JsonResponse
    {
        return new JsonResponse(['error' => $message], $status);
    }

    private function validationErrors(ConstraintViolationListInterface $violations): JsonResponse
    {
        $nameConverter = new CamelCaseToSnakeCaseNameConverter();
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$nameConverter->normalize($violation->getPropertyPath())] = (string) $violation->getMessage();
        }

        return new JsonResponse(['error' => 'Validation failed.', 'errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
