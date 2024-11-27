<?php

namespace App\Infrastructure\ForProduction\Services;

use App\Application\Ports\Services\IIdProvider;
use Symfony\Component\Uid\Uuid;

class RandomIdProvider implements IIdProvider {
  public function getId() {
    return Uuid::v7()->toString();
  }
}