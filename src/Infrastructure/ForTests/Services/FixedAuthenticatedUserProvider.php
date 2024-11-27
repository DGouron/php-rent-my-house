<?php

namespace App\Infrastructure\ForTests\Services;

use App\Application\Ports\Services\IUserProvider;
use App\Domain\Model\AuthenticatedUser;

class FixedAuthenticatedUserProvider implements IUserProvider {
  private AuthenticatedUser $user;

  public function __construct(AuthenticatedUser $user) {
    $this->user = $user;
  }

  public function getUser(): AuthenticatedUser {
    return $this->user;
  }
}