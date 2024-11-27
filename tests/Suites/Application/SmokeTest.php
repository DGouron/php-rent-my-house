<?php

namespace App\Tests\Suites\Application;

use App\Tests\Infrastructure\ApplicationTestCase;

class SmokeTest extends ApplicationTestCase {
  public function test_home() {
    $client = self::initialize();
    $client->request('GET', '/');

    $this->assertResponseStatusCodeSame(200);

    $response = $client->getResponse();
    $data = json_decode($response->getContent(), true);

    $this->assertEquals("Hello world", $data['message']);
  }
}