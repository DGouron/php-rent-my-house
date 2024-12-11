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
        return $this->copy($house);
      }
    }

    return null;
  }

  public function save(House $house): void {
    foreach ($this->database as $key => $value) {
      if ($value->getId() === $house->getId()) {
        $this->database[$key] = $house;
        return;
      }
    }

    $this->database[] = $house;
  }

  private function copy(House $house) {
    return new House($house->getId(), $house->getOwnerId());
  }
}