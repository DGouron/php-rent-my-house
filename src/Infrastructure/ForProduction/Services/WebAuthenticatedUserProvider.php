<?php

namespace App\Infrastructure\ForProduction\Services;

use App\Application\Ports\Services\IUserProvider;
use App\Domain\Entity\User;
use App\Domain\Model\AuthenticatedUser;
use Symfony\Bundle\SecurityBundle\Security;

class WebAuthenticatedUserProvider implements IUserProvider {
  public function __construct(private Security $security) {}

  public function getUser(): AuthenticatedUser {
    $user = $this->security->getUser();
    if (!($user instanceof User)) {
      throw new \Exception("User not found");
    }

    return new AuthenticatedUser($user->getId());
  }
}