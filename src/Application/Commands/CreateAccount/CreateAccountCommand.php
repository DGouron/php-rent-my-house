<?php

namespace App\Application\Commands\CreateAccount;

use Symfony\Component\Validator\Constraints as Assert;

class CreateAccountCommand {
  #[Assert\NotBlank(message: 'Email address is required')]
  #[Assert\Email(message: 'Invalid email address')]
  private readonly string $emailAddress;

  #[Assert\NotBlank(message: 'Password is required')]
  private readonly string $password;

  public function __construct(string $emailAddress, string $password) {
    $this->emailAddress = $emailAddress;
    $this->password = $password;
  }

  public function getEmailAddress(): string {
    return $this->emailAddress;
  }

  public function getPassword(): string {
    return $this->password;
  }
}