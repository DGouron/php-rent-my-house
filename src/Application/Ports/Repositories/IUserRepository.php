<?php

namespace App\Application\Ports\Repositories;

use App\Domain\Entity\User;

interface IUserRepository {
  public function findById(string $id): ?User;

  public function findByEmailAddress(string $emailAddress): ?User;

  public function save(User $user);
}