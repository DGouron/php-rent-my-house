<?php

namespace App\Application\Commands\ReserveHouse;

class ReserveHouseCommand {
  private readonly string $startDate;

  private readonly string $endDate;

  public function __construct(string $startDate, string $endDate) {
    $this->startDate = $startDate;
    $this->endDate = $endDate;
  }

  public function getStartDate(): string {
    return $this->startDate;
  }

  public function getEndDate(): string {
    return $this->endDate;
  }
}