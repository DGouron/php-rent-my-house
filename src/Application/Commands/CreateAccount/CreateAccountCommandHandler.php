<?php

namespace App\Application\Commands\CreateAccount;

use App\Application\Exception\BadRequestException;
use App\Application\Ports\Repositories\IUserRepository;
use App\Application\Ports\Services\IIdProvider;
use App\Application\Ports\Services\IPasswordHasher;
use App\Application\ViewModel\IdViewModel;
use App\Domain\Entity\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateAccountCommandHandler {
  public function __construct(
    private readonly IUserRepository $userRepository,
    private readonly IIdProvider     $idProvider,
    private readonly IPasswordHasher $passwordHasher
  ) {
  }

  public function __invoke(CreateAccountCommand $command): IdViewModel {
    $existingUser = $this->userRepository->findByEmailAddress($command->getEmailAddress());
    if ($existingUser !== null) {
      throw new BadRequestException("The e-mail address is not available");
    }

    $user = new User();
    $user->setId($this->idProvider->getId());
    $user->setEmailAddress($command->getEmailAddress());
    $user->setPassword(
      $this->passwordHasher->hash($user, $command->getPassword())
    );

    $this->userRepository->save($user);
    return new IdViewModel($user->getId());
  }
}