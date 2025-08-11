<?php

namespace App\Domain\Entity;

use DateTime;

class Reservation {
    private string $id;
    private string $houseId;
    private DateTime $startDate;
    private DateTime $endDate;

    public function __construct(string $id, string $houseId, DateTime $startDate, DateTime $endDate) {
        $this->id = $id;
        $this->houseId = $houseId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

   public function getId(): string {
    return $this->id;
   }

   public function getHouseId(): string {
    return $this->houseId;
   }

   public function getStartDate(): DateTime {
    return $this->startDate;
   }

   public function getEndDate(): DateTime {
    return $this->endDate;
   }
}