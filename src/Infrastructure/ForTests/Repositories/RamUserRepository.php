<?php

namespace App\Infrastructure\ForTests\Repositories;

use App\Application\Ports\Repositories\IHouseRepository;
use App\Application\Ports\Repositories\IUserRepository;
use App\Domain\Entity\House;
use App\Domain\Entity\User;

class RamUserRepository implements IUserRepository {
  private array $database = [];

  public function __construct(array $database = []) {
    $this->database = $database;
  }

  public function findById(string $id): ?User {
    foreach ($this->database as $user) {
      if ($user->getId() === $id) {
        return $this->copy($user);
      }
    }

    return null;
  }

  public function save(User $user): void {
    foreach ($this->database as $key => $value) {
      if ($value->getId() === $user->getId()) {
        $this->database[$key] = $user;
        return;
      }
    }

    $this->database[] = $user;
  }

  private function copy(User $user) {
    return User::create($user->getId(), $user->getEmailAddress(), $user->getPassword());
  }
}