<?php

namespace App\Infrastructure\ForProduction\Repository;

use App\Application\Ports\Repositories\IReservationRepository;
use App\Domain\Entity\House;
use App\Domain\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SqlReservationRepository extends ServiceEntityRepository implements IReservationRepository {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, Reservation::class);
  }

  public function findById(string $id): ?Reservation {
    return $this->find($id);
  }

  public function save(Reservation $reservation): void {
    $em = $this->getEntityManager();

    $house = $em->getReference(House::class, $reservation->getHouseId());
    $reservation->setHouse($house);

    $em->persist($reservation);
  }
}