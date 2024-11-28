<?php

namespace App\Lib;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class AppController extends AbstractController {
  private MessageBusInterface $commandBus;

  public function __construct(MessageBusInterface $commandBus) {
    $this->commandBus = $commandBus;
  }

  public function dispatch($command) {
    $envelope = $this->commandBus->dispatch($command);
    $response = $envelope->last(HandledStamp::class)->getResult();

    return $this->json($response);
  }
}