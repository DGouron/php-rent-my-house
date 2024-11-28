<?php

namespace App\Domain\Entity;

use DateTime;

class Reservation {
  private string $id;

  private string $houseId;

  private string $tenantId;

  private DateTime $startDate;

  private DateTime $endDate;

  private House $house;

  public function __construct(string $id, string $houseId, string $tenantId, DateTime $startDate, DateTime $endDate) {
    $this->id = $id;
    $this->houseId = $houseId;
    $this->tenantId = $tenantId;
    $this->startDate = $startDate;
    $this->endDate = $endDate;
  }

  public function getId(): string {
    return $this->id;
  }

  public function getHouseId(): string {
    return $this->houseId;
  }

  public function getTenantId(): string {
    return $this->tenantId;
  }

  public function getStartDate(): DateTime {
    return $this->startDate;
  }

  public function getEndDate(): DateTime {
    return $this->endDate;
  }

  public function setHouse(House $house): void {
    $this->house = $house;
  }
}