<?php

namespace App\Application\Ports\Services;

use App\Tests\Unit\Commands\AuthenticatedUser;

interface IUserProvider {
  public function getUser(): AuthenticatedUser;
}