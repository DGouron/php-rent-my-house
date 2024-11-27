<?php

namespace App\Infrastructure\ForTests\Services;

use App\Application\Ports\Services\IIdProvider;

class FixedIdProvider implements IIdProvider {
  private string $id;

  public function __construct(string $id) {
    $this->id = $id;
  }

  public function getId(): string {
    return $this->id;
  }
}