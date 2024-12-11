<?php

namespace App\Infrastructure\ForProduction\Services;

use App\Application\Ports\Services\IMailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class AppMailer implements IMailer {
  public function __construct(private readonly MailerInterface $mailer) {
  }

  public function send(Email $email): void {
    $this->mailer->send($email);
  }
}