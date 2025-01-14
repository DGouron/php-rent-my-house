<?php

namespace App\Tests\Suites\Application;

use App\Domain\Entity\User;
use App\Tests\Fixtures\UserFixture;
use App\Tests\Infrastructure\ApplicationTestCase;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class LoginTest extends ApplicationTestCase {
  public function setUp(): void {
    parent::setUp();
    self::initialize();

    $user = new UserFixture(
      User::create("anthony", "anthony@ancyracademy.fr", "azerty")
    );

    $this->load([$user]);
  }

  public function test_happyPath() {
    $this->request('POST', '/api/login_check', [
      'username' => 'anthony@ancyracademy.fr',
      'password' => 'azerty',
    ]);

    $this->assertResponseStatusCodeSame(200);

    $response = self::$client->getResponse();
    $data = json_decode($response->getContent(), true);

    $token = $data["token"];

    /** @var JWTEncoderInterface $encoder */
    $encoder = self::getContainer()->get(JWTEncoderInterface::class);
    $decoded = $encoder->decode($token);

    $this->assertEquals("anthony@ancyracademy.fr", $decoded["username"]);
  }

  public function test_invalidCredentials() {
    $this->request('POST', '/api/login_check', [
      'username' => 'anthony@ancyracademy.fr',
      'password' => 'wrong password',
    ]);

    $this->assertResponseStatusCodeSame(401);

    $response = self::$client->getResponse();
    $data = json_decode($response->getContent(), true);

    $this->assertEquals(401, $data["code"]);
    $this->assertEquals("Invalid credentials.", $data["message"]);
  }
}