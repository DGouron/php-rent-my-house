<?php

namespace App\Application\Commands\ReserveHouse;

class ReserveHouseCommand {
  private readonly string $houseId;

  private readonly string $startDate;

  private readonly string $endDate;

  public function __construct(string $houseId, string $startDate, string $endDate) {
    $this->houseId = $houseId;
    $this->startDate = $startDate;
    $this->endDate = $endDate;
  }

  public function getStartDate(): string {
    return $this->startDate;
  }

  public function getEndDate(): string {
    return $this->endDate;
  }

  public function getHouseId(): string {
    return $this->houseId;
  }
}