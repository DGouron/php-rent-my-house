<?php

namespace App\Application\Ports\Repositories;

use App\Domain\Entity\Reservation;

interface IReservationRepository {
  public function findById(string $id): ?Reservation;
  public function save(Reservation $reservation);
}