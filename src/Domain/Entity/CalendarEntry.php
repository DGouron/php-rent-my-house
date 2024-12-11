<?php

namespace App\Domain\Entity;

use DateTime;

enum EntryStatus: string {
  case PENDING = 'pending';
  case ACCEPTED = 'accepted';
}

class CalendarEntry {
  private string $id;

  private DateTime $startDate;

  private DateTime $endDate;

  private EntryStatus $status;

  private HouseCalendar $houseCalendar;

  public function __construct(string $id, DateTime $startDate, DateTime $endDate, EntryStatus $status, HouseCalendar $houseCalendar) {
    $this->id = $id;
    $this->startDate = $startDate;
    $this->endDate = $endDate;
    $this->status = $status;
    $this->houseCalendar = $houseCalendar;
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

  public function getStatus(): EntryStatus {
    return $this->status;
  }

  public function accept() {
    $this->status = EntryStatus::ACCEPTED;
  }
}