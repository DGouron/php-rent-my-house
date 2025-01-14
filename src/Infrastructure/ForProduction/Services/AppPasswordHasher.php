<?php

namespace App\Infrastructure\ForProduction\Services;

use App\Application\Ports\Services\IPasswordHasher;
use App\Domain\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppPasswordHasher implements IPasswordHasher {
  public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher) {
  }

  public function hash(User $user, string $password): string {
    return $this->userPasswordHasher->hashPassword($user, $password);
  }
}