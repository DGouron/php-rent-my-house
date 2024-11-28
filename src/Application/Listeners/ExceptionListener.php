<?php

namespace App\Application\Listeners;

use App\Application\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListener {
  public function __invoke(ExceptionEvent $event) {
    $exception = $event->getThrowable();
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
    }

    $response->setContent(json_encode($output));
    $event->setResponse($response);
  }
}