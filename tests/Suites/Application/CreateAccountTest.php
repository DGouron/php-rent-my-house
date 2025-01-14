<?php

namespace App\Tests\Suites\Application;

use App\Application\Ports\Repositories\IUserRepository;
use App\Tests\Infrastructure\ApplicationTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAccountTest extends ApplicationTestCase {
  public function setUp(): void {
    parent::setUp();
    self::initialize();
  }

  public function test_happyPath() {
    $this->request('POST', '/api/create-account', [
      'emailAddress' => 'anthony@ancyracademy.fr',
      'password' => 'azerty',
    ]);

    $this->assertResponseStatusCodeSame(200);

    $response = self::$client->getResponse();
    $data = json_decode($response->getContent(), true);
    $id = $data["id"];

    /** @var IUserRepository $userRepository */
    $userRepository = self::getContainer()->get(IUserRepository::class);
    $user = $userRepository->findById($id);

    $this->assertNotNull($user);
    $this->assertEquals("anthony@ancyracademy.fr", $user->getEmailAddress());

    /** @var UserPasswordHasherInterface $passwordHasher */
    $passwordHasher = self::getContainer()->get('security.password_hasher');
    $this->assertTrue($passwordHasher->isPasswordValid($user, 'azerty'));
  }
}