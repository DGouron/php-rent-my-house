<?php

namespace App\Infrastructure\ForTests\Services;

use App\Application\Ports\Services\IMailer;
use Symfony\Component\Mime\Email;

class RamMailer implements IMailer {
  /** @var Email[] */
  public array $inbox = [];

  public function send(Email $email): void {
    $this->inbox[] = $email;
  }
}