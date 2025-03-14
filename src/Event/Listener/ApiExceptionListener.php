<?php

declare(strict_types=1);

namespace BlueGrid\Event\Listener;

use Choks\DtoBundle\Exception\ValidationException as DtoValidationException;
use Choks\PasswordPolicy\Exception\PolicyCheckException;
use Choks\PasswordPolicy\Violation\Violation;
use ControlBit\Dto\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Throwable;

/**
 * A listener that listens for exception that is not caught, and puts readable response for user
 */
final class ApiExceptionListener
{
    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        $response = match (true) {
            $throwable instanceof ValidationException    => null,
            $throwable instanceof HttpExceptionInterface => $this->getHttpExceptionResponse($throwable),
            default                                      => $this->getAnyOtherExceptionResponse($throwable),
        };

        // This null thing is a hack because I need to Fix DTO mapper
        // to stop propagation, in next release
        if (null === $response) {
            return;
        }

        $event->setResponse($response);
    }

    private function getHttpExceptionResponse(HttpExceptionInterface $throwable): Response
    {
        return new JsonResponse(
            [
                'error' => [
                    'message' => $throwable->getMessage(),
                ],
            ],
            $throwable->getStatusCode()
        );
    }

    private function getAnyOtherExceptionResponse(Throwable $throwable): Response
    {
        if ($this->kernel->getEnvironment() === 'prod') {
            return new JsonResponse(
                [
                    'error' =>
                        [
                            'message' => 'We apologize, something went wrong on our side.',
                        ],
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(
            [
                'error' => [
                    'message' => $throwable->getMessage(),
                    'stack'   => $throwable->getTrace(),
                ],
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
