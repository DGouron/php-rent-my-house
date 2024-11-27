<?php

namespace App\Infrastructure\ForTests\Repositories;

use App\Application\Ports\Repositories\IReservationRepository;
use App\Domain\Entity\Reservation;

class RamReservationRepository implements IReservationRepository {
  private ?Reservation $reservation = null;

  public function save(Reservation $reservation) {
    $this->reservation = $reservation;
  }

  public function findById(string $id): ?Reservation {
    return $this->reservation;
  }
}