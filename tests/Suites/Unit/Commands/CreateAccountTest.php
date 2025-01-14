<?php

namespace App\Tests\Suites\Unit\Commands;

use App\Application\Commands\CreateAccount\CreateAccountCommand;
use App\Application\Commands\CreateAccount\CreateAccountCommandHandler;
use App\Application\Exception\BadRequestException;
use App\Domain\Entity\User;
use App\Infrastructure\ForTests\Repositories\RamUserRepository;
use App\Infrastructure\ForTests\Services\FixedIdProvider;
use App\Infrastructure\ForTests\Services\PrefixPasswordHasher;
use PHPUnit\Framework\TestCase;

class CreateAccountTest extends TestCase {
  private RamUserRepository $userRepository;

  private CreateAccountCommandHandler $commandHandler;

  protected function setUp(): void {

    $this->userRepository = new RamUserRepository([
      User::create("john-doe", "johndoe@gmail.com", "azerty")
    ]);

    $this->commandHandler = new CreateAccountCommandHandler(
      $this->userRepository,
      new FixedIdProvider("user-id"),
      new PrefixPasswordHasher()
    );
  }

  public function test_happyPath_shouldCreateAccount() {
    $command = new CreateAccountCommand("anthony@ancyracademy.fr", "azerty");
    $result = ($this->commandHandler)($command);

    $user = $this->userRepository->findById($result->getId());
    $this->assertNotNull($user);
    $this->assertEquals("anthony@ancyracademy.fr", $user->getEmailAddress());
  }

  public function test_happyPath_shouldHashPassword() {
    $command = new CreateAccountCommand("anthony@ancyracademy.fr", "azerty");
    $result = ($this->commandHandler)($command);

    $user = $this->userRepository->findById($result->getId());
    $this->assertEquals("hash:azerty", $user->getPassword());
  }

  public function test_whenEmailAddressIsUnavailable_shouldFail() {
    $command = new CreateAccountCommand("johndoe@gmail.com", "azerty");

    try {
      $result = ($this->commandHandler)($command);
      $this->fail("The e-mail address is not available");
    } catch (\Exception $e) {
      $this->assertInstanceOf(BadRequestException::class, $e);
      $this->assertEquals("The e-mail address is not available", $e->getMessage());
    }
  }
}