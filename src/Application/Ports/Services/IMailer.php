<?php

namespace App\Application\Ports\Services;

use Symfony\Component\Mime\Email;

interface IMailer {
  public function send(Email $email): void;
}