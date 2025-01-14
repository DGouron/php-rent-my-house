<?php

namespace App\Application\Ports\Services;

use App\Domain\Entity\User;

interface IPasswordHasher {
  public function hash(User $user, string $password): string;
}