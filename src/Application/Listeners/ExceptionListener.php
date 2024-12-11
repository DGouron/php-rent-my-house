<?php

namespace App\Application\Listeners;

use App\Application\Exception\BadRequestException;
use App\Application\Exception\ForbiddenException;
use App\Application\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListener {
  public function __invoke(ExceptionEvent $event) {
    $exception = $event->getThrowable();
    if ($exception instanceof HandlerFailedException) {
      $exception = $exception->getPrevious();
    }

    $response = new Response();

    $output = [
      "path" => $event->getRequest()->getPathInfo(),
      "message" => $exception->getMessage()
    ];

    if ($exception instanceof HttpExceptionInterface) {
      $response->setStatusCode($exception->getStatusCode());
      $response->headers->replace($exception->getHeaders());

      if ($exception->getPrevious() instanceof ValidationFailedException) {
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $output['message'] = $exception->getPrevious()->getMessage();
      }
    } else if ($exception instanceof NotFoundException) {
      $response->setStatusCode(Response::HTTP_NOT_FOUND);
    } else if ($exception instanceof ForbiddenException) {
      $response->setStatusCode(Response::HTTP_FORBIDDEN);
    } else if ($exception instanceof BadRequestException) {
      $response->setStatusCode(Response::HTTP_BAD_REQUEST);
    } else {
      $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    $response->setContent(json_encode($output));
    $event->setResponse($response);
  }
}