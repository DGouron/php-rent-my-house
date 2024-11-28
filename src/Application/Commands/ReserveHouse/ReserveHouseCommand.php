<?php

namespace App\Application\Commands\ReserveHouse;

use Symfony\Component\Validator\Constraints as Assert;

class ReserveHouseCommand {
  #[Assert\NotBlank(message: 'House Id is required')]
  private readonly string $houseId;

  #[Assert\NotBlank(message: 'Start date is required')]
  #[Assert\Regex(
    pattern: '/^\d{4}-\d{2}-\d{2}$/',
    message: 'Invalid date format'
  )]
  private readonly string $startDate;

  #[Assert\NotBlank(message: 'End date is required')]
  #[Assert\Regex(
    pattern: '/^\d{4}-\d{2}-\d{2}$/',
    message: 'Invalid date format'
  )]
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