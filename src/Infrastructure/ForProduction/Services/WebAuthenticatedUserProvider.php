<?php

namespace App\Infrastructure\ForProduction\Services;

use App\Application\Ports\Services\IUserProvider;
use App\Domain\Model\AuthenticatedUser;

class WebAuthenticatedUserProvider implements IUserProvider {
  public function getUser(): AuthenticatedUser {
    return new AuthenticatedUser("web-user");
  }
}