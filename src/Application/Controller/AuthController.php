<?php

namespace App\Application\Controller;

use App\Application\Commands\CreateAccount\CreateAccountCommand;
use App\Lib\AppController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AppController {
  #[Route('/api/create-account', format: "json")]
  public function createAccount(#[MapRequestPayload] CreateAccountCommand $command) {
    return $this->dispatch($command);
  }
}