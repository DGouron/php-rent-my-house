<?php

namespace App\Domain\Entity;

class House {
  private string $id;

  private string $ownerId;

  private User $owner;

  public function __construct(string $id, string $ownerId) {
    $this->id = $id;
    $this->ownerId = $ownerId;

  }

  public function getId(): string {
    return $this->id;
  }

  public function getOwnerId(): string {
    return $this->ownerId;
  }

  public function setOwnerId(string $ownerId): void {
    $this->ownerId = $ownerId;
  }

  public function getOwner(): User {
    return $this->owner;
  }

  public function setOwner(User $owner): void {
    $this->owner = $owner;
  }
}