<?php

namespace App\Lib;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class AppController extends AbstractController {
  private MessageBusInterface $commandBus;
  private MessageBusInterface $queryBus;

  public function __construct(MessageBusInterface $commandBus, MessageBusInterface $queryBus) {
    $this->commandBus = $commandBus;
    $this->queryBus = $queryBus;
  }

  public function dispatch($command) {
    $envelope = $this->commandBus->dispatch($command);
    $response = $envelope->last(HandledStamp::class)->getResult();

    return $this->json($response);
  }

  public function dispatchQuery($query) {
    $envelope = $this->queryBus->dispatch($query);
    $response = $envelope->last(HandledStamp::class)->getResult();

    return $this->json($response);
  }
}