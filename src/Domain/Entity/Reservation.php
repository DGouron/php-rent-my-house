<?php

namespace App\Domain\Entity;

use DateTime;

class Reservation {
    private string$id;
    private DateTime $startDate;
    private DateTime $endDate;

    public function __construct(string $id, DateTime $startDate, DateTime $endDate) {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

   public function getId(): string {
    return $this->id;
   }

   public function getStartDate(): DateTime {
    return $this->startDate;
   }

   public function getEndDate(): DateTime {
    return $this->endDate;
   }
}