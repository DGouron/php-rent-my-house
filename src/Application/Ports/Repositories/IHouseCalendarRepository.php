<?php

namespace App\Application\Ports\Repositories;

use App\Domain\Entity\HouseCalendar;

interface IHouseCalendarRepository {
  public function findById(string $id): ?HouseCalendar;

  public function save(HouseCalendar $houseCalendarCalendar): void;
}