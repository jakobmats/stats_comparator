<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $response = $this->createApiResponse($exception);
        $event->setResponse($response);
    }

    private function createApiResponse(Throwable $exception): Response
    {
        $statusCode = $exception instanceof HttpException ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;

        return new JsonResponse([
            'error' => $exception->getMessage()
        ], $statusCode);
    }
}