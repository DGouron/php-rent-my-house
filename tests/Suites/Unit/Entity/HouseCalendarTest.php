<?php

namespace App\Tests\Suites\Unit\Entity;

use App\Domain\Entity\HouseCalendar;
use App\Domain\Entity\Reservation;
use App\Domain\Entity\ReservationStatus;
use DateTime;
use PHPUnit\Framework\TestCase;

class HouseCalendarTest extends TestCase {
  public function test_isAvailable() {
    $calendar = new HouseCalendar("house-id");
    $reservation = new Reservation(
      "reservation-id",
      "house-id",
      "user-id",
      new DateTime("2024-01-05"),
      new DateTime("2024-01-07"),
      ReservationStatus::ACCEPTED
    );

    $calendar->addReservation($reservation);

    // Outside ranges
    $this->assertTrue($calendar->isAvailable(new DateTime("2024-01-01"), new DateTime("2024-01-04")));
    $this->assertFalse($calendar->isAvailable(new DateTime("2024-01-05"), new DateTime("2024-01-07")));
    $this->assertTrue($calendar->isAvailable(new DateTime("2024-01-08"), new DateTime("2024-01-10")));

    // Inside range
    $this->assertFalse($calendar->isAvailable(new DateTime("2024-01-04"), new DateTime("2024-01-06")));
    $this->assertFalse($calendar->isAvailable(new DateTime("2024-01-06"), new DateTime("2024-01-08")));
  }
}