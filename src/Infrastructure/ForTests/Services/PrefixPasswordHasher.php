<?php

namespace App\Infrastructure\ForTests\Services;

use App\Application\Ports\Services\IPasswordHasher;
use App\Domain\Entity\User;

class PrefixPasswordHasher implements IPasswordHasher {
  public function hash(User $user, string $password): string {
    return "hash:" . $password;
  }
}