<?php

namespace App\Domain\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface {
  private string $id;

  private string $emailAddress;

  private string $password;

  public function getId(): string {
    return $this->id;
  }

  public function getPassword(): ?string {
    return $this->password;
  }

  public function getRoles(): array {
    return ["ROLE_USER"];
  }

  public function eraseCredentials(): void {
    // Nothing to do here
  }

  public function getUserIdentifier(): string {
    return $this->emailAddress;
  }

  /**
   * @param string $id
   */
  public function setId(string $id): void {
    $this->id = $id;
  }

  /**
   * @param string $emailAddress
   */
  public function setEmailAddress(string $emailAddress): void {
    $this->emailAddress = $emailAddress;
  }

  /**
   * @param string $password
   */
  public function setPassword(string $password): void {
    $this->password = $password;
  }
}