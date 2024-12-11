<?php

namespace App\Infrastructure\ForTests\Repositories;

use App\Application\Ports\Repositories\IReservationRepository;
use App\Domain\Entity\Reservation;

class RamReservationRepository implements IReservationRepository {

  public function __construct(
    /** @var Reservation[] */
    private array $database = []
  ) {}

  public function save(Reservation $reservation) {
   foreach($this->database as $key => $value) {
     if($value->getId() === $reservation->getId()) {
       $this->database[$key] = $reservation;
       return;
     }
   }

    $this->database[] = $reservation;
  }

  public function findById(string $id): ?Reservation {
    foreach ($this->database as $reservation) {
      if ($reservation->getId() === $id) {
        return $this->copy($reservation);
      }
    }

    return null;
  }

  private function copy(Reservation $entity): Reservation {
    return new Reservation(
      $entity->getId(),
      $entity->getHouseId(),
      $entity->getTenantId(),
      $entity->getStartDate(),
      $entity->getEndDate(),
      $entity->getStatus()
    );
  }
}