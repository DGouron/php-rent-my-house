<?php

namespace App\Application\Ports\Services;

use App\Domain\Model\AuthenticatedUser;

interface IUserProvider {
  public function getUser(): AuthenticatedUser;
}