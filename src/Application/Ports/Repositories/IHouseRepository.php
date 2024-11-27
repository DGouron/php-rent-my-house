<?php

namespace App\Application\Ports\Repositories;

use App\Domain\Entity\House;

interface IHouseRepository {
  public function findById(string $id): ?House;

  public function save(House $house): void;
}