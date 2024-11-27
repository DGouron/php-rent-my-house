<?php

namespace App\Infrastructure\ForTests\Repositories;

use App\Application\Ports\Repositories\IHouseRepository;
use App\Domain\Entity\House;

class RamHouseRepository implements IHouseRepository {
  private array $database = [];

  public function __construct(array $database = []) {
    $this->database = $database;
  }

  public function findById(string $id): ?House {
    foreach ($this->database as $house) {
      if ($house->getId() === $id) {
        return $house;
      }
    }

    return null;
  }
}