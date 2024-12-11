<?php

namespace App\Infrastructure\ForTests\Repositories;

use App\Application\Ports\Repositories\IHouseCalendarRepository;
use App\Domain\Entity\HouseCalendar;

class RamHouseCalendarRepository implements IHouseCalendarRepository {
  private array $database = [];

  public function __construct(array $database = []) {
    $this->database = $database;
  }

  public function findById(string $id): ?HouseCalendar {
    foreach ($this->database as $houseCalendar) {
      if ($houseCalendar->getId() === $id) {
        return $this->copy($houseCalendar);
      }
    }

    return null;
  }

  public function save(HouseCalendar $houseCalendar): void {
    foreach ($this->database as $key => $value) {
      if ($value->getId() === $houseCalendar->getId()) {
        $this->database[$key] = $houseCalendar;
        return;
      }
    }

    $this->database[] = $houseCalendar;
  }

  private function copy(HouseCalendar $houseCalendar) {
    return new HouseCalendar($houseCalendar->getId(), $houseCalendar->getEntries()->toArray());
  }
}